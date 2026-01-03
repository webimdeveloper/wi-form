export function calculateTotals(configRoot = {}, rows = [], mode = 'company') {
  const cfg = configRoot?.[mode] || {};
  const bucToUzs = Number(configRoot?.buc_to_uzs) || 412000;
  const usdToUzs = Number(configRoot?.usd_to_uzs) || 12000;
  const bucToUsd = bucToUzs / usdToUzs;

  let totalBUC = 0;
  let classes = 0;

  const safeRows = Array.isArray(rows) && rows.length ? rows : [{ id: 'row-1', classes: 1 }];

  safeRows.forEach((row) => {
    const value = Number(row.classes);
    const clamped = Number.isFinite(value) && value >= 1 ? value : 1;
    classes += clamped;

    const extra = Math.max(clamped - 1, 0);
    const submitBUC = Number(cfg.submit_first || 0) + Number(cfg.submit_additional || 0) * extra;
    const certBUC = Number(cfg.cert_first || 0) + Number(cfg.cert_additional || 0) * extra;
    totalBUC += submitBUC + certBUC;
  });

  const stateDutyUSD = Math.round(totalBUC * bucToUsd);
  const serviceUSD = Math.round((Number(cfg.service_per_tm_usd) || 0) * safeRows.length);
  const totalUSD = Math.round(stateDutyUSD + serviceUSD);

  return {
    mode,
    trademarks: safeRows.length,
    classes,
    totals: {
      stateDutyUSD,
      serviceUSD,
      totalUSD,
    },
  };
}

export default calculateTotals;

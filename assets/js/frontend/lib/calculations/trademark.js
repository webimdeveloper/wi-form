export function calculateTotals(configRoot = {}, rows = [], mode = 'company') {
  const cfg = configRoot?.[mode] || {};
  const bucUzs = Number(configRoot?.buc_uzs) || 412000;
  const usdToUzs = Number(configRoot?.usd_to_uzs) || 12000;
  // Prevent division by zero if rate is missing
  const exchangeRate = usdToUzs > 0 ? usdToUzs : 12000;

  let totalSubmitBUC = 0;
  let totalCertBUC = 0;
  let classes = 0;

  const safeRows = Array.isArray(rows) && rows.length ? rows : [{ id: 'row-1', classes: 1 }];
  const classCounts = [];
  
  safeRows.forEach((row) => {
    const value = Number(row.classes);
    const clamped = Number.isFinite(value) && value >= 1 ? value : 1;
    classCounts.push(clamped);
    classes += clamped;

    const extra = Math.max(clamped - 1, 0);
    const submitBUC = Number(cfg.submit_first || 0) + Number(cfg.submit_additional || 0) * extra;
    const certBUC = Number(cfg.cert_first || 0) + Number(cfg.cert_additional || 0) * extra;
    
    totalSubmitBUC += submitBUC;
    totalCertBUC += certBUC;
  });

  // Calculate Base State Duties in UZS
  const stateDutySubmitUZS = totalSubmitBUC * bucUzs;
  const stateDutyCertUZS = totalCertBUC * bucUzs;
  const stateDutyUZS = stateDutySubmitUZS + stateDutyCertUZS;

  // Calculate Service Fee in UZS
  const servicePerTmUZS = Number(cfg.service_fee_uzs) || 0;
  const serviceUZS = servicePerTmUZS * safeRows.length;
  const totalUZS = stateDutyUZS + serviceUZS;

  // Convert to USD and round
  const stateDutySubmitUSD = Math.round(stateDutySubmitUZS / exchangeRate);
  const stateDutyCertUSD = Math.round(stateDutyCertUZS / exchangeRate);
  const stateDutyUSD = stateDutySubmitUSD + stateDutyCertUSD;
  const serviceUSD = Math.round(serviceUZS / exchangeRate);
  const totalUSD = Math.round(totalUZS / exchangeRate);

  return {
    mode,
    trademarks: safeRows.length,
    classes,
    classCounts,
    totals: {
      // Primary USD values for display
      stateDutySubmitUSD,
      stateDutyCertUSD,
      stateDutyUSD,
      serviceUSD,
      totalUSD,
      // Optional: Pass UZS values if needed for debugging/display
      stateDutySubmitUZS,
      stateDutyCertUZS,
      stateDutyUZS,
      serviceUZS,
      totalUZS,
    },
  };
}

export default calculateTotals;

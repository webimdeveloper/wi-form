# Trademark Calculator – MVP Logic Documentation

This document defines the current implementation of the Trademark Registration Calculator inside the WiForm plugin.

---

## 1. Overview
Shortcode: `[wi_form_trademark]`

The calculator includes:
- PHP shortcode (outputs UI + passes settings to JS)
- JavaScript engine (calculations + UX)
- Minimal CSS

---

## 2. Settings Model
Settings passed from PHP to JS:

```json
{
  "buc_to_uzs": 412000,
  "usd_to_uzs": 12000,
  "company": {
    "submit_first": 6,
    "submit_additional": 1,
    "cert_first": 11.6,
    "cert_additional": 4,
    "service_per_tm_usd": 200
  },
  "private": {
    "submit_first": 4,
    "submit_additional": 0.5,
    "cert_first": 6.8,
    "cert_additional": 1,
    "service_per_tm_usd": 200
  }
}
```

JS computes:
```
1 BUC in USD = buc_to_uzs / usd_to_uzs
```
Default ≈ 34.33 USD.

---

## 3. Pricing Model
### Company
- Submit: 6 BUC + 1 BUC per extra class
- Certificate: 11.6 BUC + 4 BUC per extra class
- Service: $200 per trademark

### Private
- Submit: 4 BUC + 0.5 BUC per extra class
- Certificate: 6.8 BUC + 1 BUC per extra class
- Service: $200 per trademark

---

## 4. UI Structure
- Radio buttons: Company / Private
- Repeater rows: Class count (1–100), Add/Delete
- Email field (hidden until Calculate)
- Calculate button
- Results section (hidden until Calculate)

---

## 5. JS Logic Summary
### Repeater
- Add row → clones first row
- Remove row → allowed only for rows > 1

### Calculation Per Trademark
```
extra = classes - 1
submitBUC = submit_first + submit_additional * extra
certBUC   = cert_first   + cert_additional * extra
tmBUC = submitBUC + certBUC
```

### Total Across All Rows
```
totalBUC = sum of all tmBUC
stateDutyUSD = totalBUC * (buc_to_uzs / usd_to_uzs)
serviceUSD = trademarkCount * service_per_tm_usd
totalUSD = stateDutyUSD + serviceUSD
```

All values rounded.

---

## 6. Output
JS fills:
- `.wiform-result-trademarks`
- `.wiform-result-classes`
- `.wiform-result-state-duty`
- `.wiform-result-service`
- `.wiform-result-total`

Then shows:
- `.wiform-results`
- `.wiform-field--email`

---

## 7. Known Limitations
- No admin settings yet
- Email not used
- Very basic styling
- No validation
- Not part of builder architecture yet

---

## 8. Planned Extensions
- Admin rate settings UI
- Styling improvements
- Email sending + PDF quote
- Multi-step format
- Integration into full form builder (elements, formulas, repeater logic)

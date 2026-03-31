    # Technical Task: Calculator Plugin Expansion (v2)






##THE original message from the client
1. Опция «Поиск (проверка товарного знака)»

Необходимо добавить отдельную опцию «Поиск», при выборе которой пользователь сможет выбрать тип знака:

Словесный знак
Услуги: 1 500 000 UZS (+ 500 000 UZS за каждый дополнительный класс)

Изобразительный знак 
Услуги: 2 000 000 UZS (+ 700 000 UZS за каждый дополнительный класс)

Комбинированный знак
Услуги: 2 500 000 UZS (+ 800 000 UZS за каждый дополнительный класс)

Примечания:
A) Количество классов уже реализовано в калькуляторе, поэтому можно использовать существующую логику без дополнительных изменений.
B) Указанные суммы по опции «Поиск» являются исключительно стоимостью наших услуг и не привязаны к БРВ.
C) При выборе пользователем опции «Поиск» она должна учитываться и корректно отображаться в итоговом расчете (в финальной сумме калькулятора).
D) Прошу также предусмотреть отображение всех рассчитанных сумм в долларовом эквиваленте (USD) наряду с UZS.

2. Опция «Ускоренная экспертиза»

Необходимо добавить отдельную опцию «Ускоренная экспертиза», также с выбором типа знака:

Словесный знак
Государственный сбор: 8 БРВ (+ 1 БРВ за каждый дополнительный класс) + 12% НДС
Услуги: 1 200 000 UZS

Изобразительный знак
Государственный сбор: 10 БРВ (+ 1 БРВ за каждый дополнительный класс) + 12% НДС
Услуги: 1 200 000 UZS

Комбинированный знак
Государственный сбор: 12 БРВ (+ 1 БРВ за каждый дополнительный класс) + 12% НДС
Услуги: 1 200 000 UZS

Примечания:
A) Количество классов уже реализовано в калькуляторе, поэтому можно использовать существующую логику.
B) В данной опции часть расчета привязана к БРВ (госпошлина), а часть фиксированная стоимость наших услуг. 
C) При выборе пользователем опции «Ускоренная экспертиза» она должна учитываться и корректно отображаться в итоговом расчете (в финальной сумме калькулятора) - причем можно даже показать итоговую сумму без разбивки на государственный сбор и услуги.
D) Также отображаем цены в долларовой эквиваленте. 
E) 1 БРВ = 412 000 UZS



## 1. Suggested task. SHOULD be verified before implementation to keep the logic clear

The current trademark registration calculator plugin needs to be expanded with two new optional services: "Trademark clearance search" and "Accelerated Examination". The existing calculation logic must remain fully intact. All new pricing variables must be added to the WordPress admin panel so the client can update them without altering the code.

## 2. WordPress Admin Panel (Settings Page Updates)
Add the following configurable fields to the existing plugin settings page. Default values are provided.

**General Variables:**
* `BUC_value`: 412,000 (UZS)
* `VAT_rate`: 0.12 (12%)
* `USD_exchange_rate`: [Enter current rate] (Required to calculate the USD equivalent on the frontend)

**Option 1: Trademark Clearance Search (Services Cost - UZS)**
* `search_word_base`: 1,500,000
* `search_word_extra`: 500,000
* `search_fig_base`: 2,000,000
* `search_fig_extra`: 700,000
* `search_comb_base`: 2,500,000
* `search_comb_extra`: 800,000

**Option 2: Accelerated Examination (State Fee bucs & Service Cost)**
* `accel_state_word_base_buc`: 8
* `accel_state_fig_base_buc`: 10
* `accel_state_comb_base_buc`: 12
* `accel_state_extra_buc`: 1 (Applies to all types)
* `accel_service_cost_uzs`: 1,200,000 (Applies to all types)

## 3. Frontend UI Updates
For each trademark item added by the user, introduce the following controls before the "Calculate" button:

1.  **Checkbox:** `[ ] Trademark clearance search` (Unchecked by default)
2.  **Checkbox:** `[ ] Accelerated Examination` (Unchecked by default)
3.  **Radio Group / Select:** "Trademark Type"
    * Options: `Word Trademark`, `Figurative Trademark`, `Combined Trademark`.
    * *UI Logic:* This selection is required if either of the checkboxes above is selected. 

**Summary / Total Area:**
* Add a new row in the final calculation output to display the total equivalent in USD (Calculated as `Total UZS / USD_exchange_rate`).

## 4. Calculation Logic & Formulas

Let `N` = Total number of classes.
Let `ExtraClasses` = `Max(0, N - 1)`.

**A. If "Trademark clearance search" is checked:**
Calculate `Search_Cost` based on the selected Trademark Type:
* **Word:** `search_word_base + (ExtraClasses * search_word_extra)`
* **Figurative:** `search_fig_base + (ExtraClasses * search_fig_extra)`
* **Combined:** `search_comb_base + (ExtraClasses * search_comb_extra)`

**B. If "Accelerated Examination" is checked:**
Calculate `Accel_Cost` based on the selected Trademark Type. Note: State fees use buc and VAT, services do not.
* **Word:** `(((accel_state_word_base_buc + (ExtraClasses * accel_state_extra_buc)) * buc_value) * (1 + VAT_rate)) + accel_service_cost_uzs`
* **Figurative:** `(((accel_state_fig_base_buc + (ExtraClasses * accel_state_extra_buc)) * buc_value) * (1 + VAT_rate)) + accel_service_cost_uzs`
* **Combined:** `(((accel_state_comb_base_buc + (ExtraClasses * accel_state_extra_buc)) * buc_value) * (1 + VAT_rate)) + accel_service_cost_uzs`

## 5. Final Output
* `Grand_Total_UZS` = `[Existing Base Logic Result]` + `Search_Cost` (if applicable) + `Accel_Cost` (if applicable).
* The output must be a single aggregated sum in UZS (no need to split state fee and service fee in the frontend UI).
* `Grand_Total_USD` = `Grand_Total_UZS / USD_exchange_rate`. Format to 2 decimal places or round to a whole number based on existing formatting.
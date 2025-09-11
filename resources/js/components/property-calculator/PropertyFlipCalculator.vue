<script setup lang="ts">
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement, ChartData
} from 'chart.js'

import { useForm } from "@inertiajs/vue3";
import {
  getCalPosNegIcon,
  getCalPosNegText,
  calculatorParams,
  DollarIcon,
  calculateLoan, getCalPosNegTextColor
} from "@/components/property-calculator";
import { ref, watch } from "vue";
import { Icon } from "@iconify/vue";
import { InputNumber } from "@/components/ui/input-number";
import { useNumber } from "@/composables/useFormat";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
  DropdownMenuItem,
  DropdownMenuGroup
} from "@/components/ui/dropdown-menu";

import Button from "@/components/ui/button/Button.vue";


const { formatPrice, formatPercent } = useNumber()
// const {formatDate} = useDateFormat()
// const {formatToCapitalizeEachWord,} = useTextFormat()

const props = defineProps<{
  params: calculatorParams | null;
  editAllowed: false,
}>()

const manualOverrides = ref<Set<string>>(new Set())

const formFlip = useForm({

  // assumptions
  purchase_price: props.params?.listPrice ?? 0,// props.params.ListPrice
  renovations: props.params?.sqft ? props.params.sqft * 50 : 0, // props.params.sqft * 50
  closing_cost: 0, // purchase_price * 0.02
  points_percentage: 0, //
  points_amount: 0, // points_amount * mortgage_amount
  total_into_deal: 0, // purchase_price + renovations + closing_cost + points_amount;
  mortgage_amount: 0, // (purchase_price + renovations) * 0.9
  purchase_plus_rehab: 0, // (purchase_price + renovations)
  mortgage_rental_percentage: 0, // mortgage_amount / purchase_plus_rehab
  interest_rate: 10, // 10%
  mortgage_term: 360, // 360
  monthly_payment: 0, // calculateLoan.monthlyPayment [mortgage_amount, interest_rate, mortgage_term, 0]
  arv: props.params?.arv ?? 0, // props.params.arv
  total_interest_per_month: 0, // calculateLoan.totalInterest / mortgage_term
  total_cash_needed: 0,//total_into_deal - mortgage_amount

  tax_amount_per_month: props.params?.taxAnnualAmt ? Number((props.params.taxAnnualAmt / 12)?.toFixed(2)) : 0, // props.params.taxAnnualAmt / 12
  insurance: props.params?.arv ? Number((props.params.arv * 0.00038)?.toFixed(2)) : 0, // props.params.arv * 0.00038
  utilities: 0,
  repair_maintain: 0, // gross_rent * 0.05
  landscaping: 0,
  misc: 0,
  total_expenses: 0, // tax_amount_per_month + insurance + utilities + repair_maintain + landscaping + misc
  monthly_carry: 0, // total_expenses + monthly_payment
  daily_carry: 0, // (monthly_carry * 12 ) / 365
  hold_duration: 180,
  hold_amount: 0, // hold_duration * daily_carry
  commissions: 5,
  commissions_amount: 0, // arv * commissions %
  transfer_tax: 1,
  transfer_tax_amount: 0,// arv * transfer_tax %
  concessions: 1,
  concessions_amount: 0, // arv * concessions %
  total_sale_debits: 0, // concessions_amount + transfer_tax_amount + commissions_amount

  //ROI
  total_cost_with_debit: 0, // (total_into_deal + total_sale_debits)
  total_cost: 0, // (total_into_deal - purchase_price) + hold_amount
  flip_profit: 0, // (arv -  total_cost - total_sale_debits + purchase_price)
  cash_on_cash: 0, // (flip_profit/total_into_deal)
  roi: 0, // (flip_profit/total_cost_with_debit)
  annualized_roi: 0,  // (roi ^ (1/(hold_duration/365)))
});

const data = ref<ChartData>({
  datasets: [{
    label: 'Cash on cash',
    data: [0],
    backgroundColor: ['#f87171'],
  }
  ],
});
const data2 = ref<ChartData>({
  datasets: [{
    label: 'ROI',
    data: [0],
    backgroundColor: ['#60a5fa'],
  },
  ],
});
const data3 = ref<ChartData>({
  datasets: [{
    label: 'Annualized ROI',
    data: [0],
    backgroundColor: ['#facc15'],
  }],
});

const options = {
  responsive: true,
  cutout: '80%',
  plugins: {
    legend: {
      display: false,
    },
    title: {
      display: false,
      text: 'ROI Rental'
    }
  }
}
const initialCalculation = () => {
  if (!isOverridden('closing_cost')) {
    formFlip.closing_cost = formFlip.purchase_price * 0.02
  }

  formFlip.purchase_plus_rehab = formFlip.purchase_price + formFlip.renovations

  if (!isOverridden('mortgage_amount')) {
    formFlip.mortgage_amount = formFlip.purchase_plus_rehab * 0.9
  }

  if (!isOverridden('points_amount')) {
    formFlip.points_amount = (formFlip.points_percentage / 100) * formFlip.mortgage_amount
  }

  formFlip.total_into_deal = formFlip.purchase_price + formFlip.renovations + formFlip.closing_cost + formFlip.points_amount
  formFlip.mortgage_rental_percentage = formFlip.mortgage_amount / formFlip.purchase_plus_rehab;
  formFlip.total_cash_needed = formFlip.total_into_deal - formFlip.mortgage_amount;
  formFlip.total_cost = (formFlip.total_into_deal - formFlip.purchase_price) + formFlip.hold_amount;
  data.value.datasets[0].data = [formFlip.cash_on_cash]
  data2.value.datasets[0].data = [formFlip.roi]
  data3.value.datasets[0].data = [formFlip.annualized_roi]

}

watch(
  () => [formFlip.purchase_price, formFlip.renovations, formFlip.closing_cost, formFlip.points_percentage],
  () => initialCalculation(),
  { immediate: true }
)

watch(
  () => [formFlip.mortgage_amount, formFlip.interest_rate, formFlip.mortgage_term],
  () => {
    const { monthlyPayment, totalInterest } = calculateLoan({
      loanAmount: formFlip.mortgage_amount,
      interestPercent: formFlip.interest_rate,
      termMonths: formFlip.mortgage_term,
      totalFee: 0,
    })

    formFlip.monthly_payment = monthlyPayment
    formFlip.total_interest_per_month = totalInterest / formFlip.mortgage_term
    formFlip.total_cash_needed = formFlip.total_into_deal - formFlip.mortgage_amount;
    formFlip.mortgage_rental_percentage = formFlip.mortgage_amount / formFlip.purchase_plus_rehab;
    formFlip.total_cost = (formFlip.total_into_deal - formFlip.purchase_price) + formFlip.hold_amount;
    data.value.datasets[0].data = [formFlip.cash_on_cash]
    data2.value.datasets[0].data = [formFlip.roi]
    data3.value.datasets[0].data = [formFlip.annualized_roi]
  },
  { immediate: true }
)

watch(
  () => [
    formFlip.tax_amount_per_month,
    formFlip.insurance,
    formFlip.utilities,
    formFlip.repair_maintain,
    formFlip.misc,
    formFlip.landscaping,
    formFlip.monthly_payment,
    formFlip.hold_duration,
    formFlip.commissions,
    formFlip.transfer_tax,
    formFlip.concessions
  ],
  () => {
    formFlip.total_expenses =
      formFlip.tax_amount_per_month +
      formFlip.insurance +
      formFlip.misc +
      formFlip.utilities +
      formFlip.landscaping +
      formFlip.repair_maintain;

    formFlip.monthly_carry = formFlip.total_expenses + formFlip.monthly_payment;
    formFlip.daily_carry = (formFlip.monthly_carry * 12) / 365;

    formFlip.hold_amount = formFlip.hold_duration * formFlip.daily_carry;
    formFlip.commissions_amount = formFlip.arv * (formFlip.commissions / 100);
    formFlip.transfer_tax_amount = formFlip.arv * (formFlip.transfer_tax / 100);
    formFlip.concessions_amount = formFlip.arv * (formFlip.concessions / 100);
    formFlip.total_sale_debits = formFlip.commissions_amount +
      formFlip.transfer_tax_amount +
      formFlip.concessions_amount;

    formFlip.total_cost_with_debit = (formFlip.total_into_deal + formFlip.total_sale_debits);
    formFlip.total_cost = (formFlip.total_into_deal - formFlip.purchase_price) + formFlip.hold_amount;
    // (arv -  total_cost - total_sale_debits + purchase_price)
    formFlip.flip_profit = (formFlip.arv - formFlip.total_cost - formFlip.total_sale_debits - formFlip.purchase_price);

    // cash_on_cash: 0, // (flip_profit/total_into_deal)
    //     roi: 0, // (flip_profit/total_cost_with_debit)
    //     annualized_roi: 0,  // ((roi+1) ^ (1/(hold_duration/365)))
    formFlip.cash_on_cash = formFlip.flip_profit / formFlip.total_into_deal;
    formFlip.roi = formFlip.flip_profit / formFlip.total_cost_with_debit;
    formFlip.annualized_roi = (formFlip.roi + 1) ** (1 / (formFlip.hold_duration / 365));
    data.value.datasets[0].data = [formFlip.cash_on_cash]
    data2.value.datasets[0].data = [formFlip.roi]
    data3.value.datasets[0].data = [formFlip.annualized_roi]
  },
  { immediate: true }
)

ChartJS.register(Title, Tooltip, Legend, ArcElement)

function markManual(field: keyof typeof formFlip) {
  const raw = formFlip[field]
  const value = Number(raw)
  if (raw === '' || raw === null || raw === undefined || isNaN(value)) {
    formFlip[field] = 0
  } else {
    formFlip[field] = value
  }
  manualOverrides.value.add(field)
}

function isOverridden(field: keyof typeof formFlip) {
  return manualOverrides.value.has(field)
}

const selectedTab = ref<string>('roi'); // assum, in_ex, roi
const onTabChanged = (tab: string) => {
  selectedTab.value = tab;
}

const resetDefault = () => {
  formFlip.reset();
  manualOverrides.value.clear();
  initialCalculation()

}

const onSave = () => {

}
</script>

<template>
  <div class="w-full px-2 max-w-lg mx-auto">
    <div class="tab-header">
      <div class="tab-bar flex">
        <div class="flex-1 text-center py-2 select-none flex justify-center"
          :class="selectedTab == 'assum' ? `font-semibold text-primary-strong border-b-2 border-primary` : ``"
          @click="onTabChanged('assum')">
          Purchase <br />Assumptions
        </div>
        <div class="flex-1 text-center py-2 select-none flex justify-center"
          :class="selectedTab == 'in_ex' ? `font-semibold text-primary-strong border-b-2 border-primary` : ``"
          @click="onTabChanged('in_ex')">
          Expenses/<br />Holding
        </div>
        <div class="flex-1 text-center py-2 select-none flex justify-center"
          :class="selectedTab == 'roi' ? `font-semibold text-primary-strong border-b-2 border-primary` : ``"
          @click="onTabChanged('roi')">
          ROI<br />Summary
        </div>
        <div class="text-center py-2 select-none flex justify-center w-fit">
          <DropdownMenu>
            <DropdownMenuTrigger as-child class="w-fit justify-center">
              <Button variant="outline">
                <Icon icon="tabler:caret-up-down" class="iconAttr" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-[160px] rounded-lg" :side="'bottom'" align="end" :side-offset="4">
              <DropdownMenuGroup>
                <DropdownMenuItem :as-child="true">
                  <Button @click="resetDefault" class="w-full flex justify-start" variant="ghost">
                    <Icon class="size-5" icon="tabler:restore"></Icon>
                    Reset Default
                  </Button>
                </DropdownMenuItem>
                <DropdownMenuItem :as-child="true">
                  <Button @click="onSave()" class="w-full flex justify-start" variant="ghost">
                    <Icon class="size-5" icon="tabler:device-floppy"></Icon>
                    Save (U/C)
                  </Button>
                </DropdownMenuItem>
              </DropdownMenuGroup>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </div>
    <div class="tab-content w-full pt-3 pb-2 px-2">
      <div class="w-full" v-if="selectedTab == 'assum'">
        <div class="grid grid-cols-2 my-auto">
          <div class="my-auto">Purchase Price</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('purchase_price')" align='right' v-model="formFlip.purchase_price"
              type="price" />
          </div>
          <div class="my-auto">Renovations</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('renovations')" align='right' v-model="formFlip.renovations"
              type="price" />
          </div>
          <div class="my-auto">Closing Costs</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('closing_cost')" align='right' v-model="formFlip.closing_cost"
              type="price" />
          </div>
          <div class="my-auto">Points(%)</div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formFlip.points_amount) }}</div>
            <div class="flex-2">
              <InputNumber @change="markManual('points_percentage')" align='right' class="max-w-[100px]"
                v-model="formFlip.points_percentage" type="percentage" />
            </div>
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">In At Closing</div>
          <div class="my-auto text-right pr-8 py-2 ">
            {{ formatPrice(formFlip.total_into_deal) }}
          </div>
          <div class="col-span-2 border-b-4 my-3 "></div>
          <div class="my-auto">Mortgage Amount</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('mortgage_amount')" align='right' v-model="formFlip.mortgage_amount"
              type="price" />
          </div>
          <div class="col-span-2 flex justify-center py-2">
            <span class="text-xs my-auto">% of purchase + rehab</span>
            <div class="">&nbsp;&nbsp;</div>
            <span class="font-medium">{{ formatPercent(formFlip.mortgage_rental_percentage) }}</span>
          </div>
          <div class="my-auto">Interest Rate</div>
          <div class="my-auto">
            <InputNumber @change="markManual('interest_rate')" align='right' v-model="formFlip.interest_rate"
              type="percentage" />
          </div>
          <div class="my-auto">Mortgage Term <span class="text-xs text-muted-foreground">(Months)</span>
          </div>
          <div class="my-auto">
            <InputNumber @change="markManual('mortgage_term')" align='right' v-model="formFlip.mortgage_term" />
          </div>
          <div class="my-auto">Monthly Payment</div>
          <div class="my-auto text-right pr-8 py-2">{{ formatPrice(formFlip.monthly_payment, 2) }}</div>
          <div class="col-span-2 border-b-4 my-3 "></div>
          <div class="my-auto">ARV</div>
          <div class="my-auto flex">
            <DollarIcon :value="true" />
            <InputNumber @change="markManual('arv')" align='right' v-model="formFlip.arv" type="price" />
          </div>
          <div class="my-auto">Total Cash Needed</div>
          <div class="my-auto text-right pr-8 py-2">{{ formatPrice(formFlip.total_cash_needed, 2) }}</div>
        </div>
      </div>
      <div class="" v-if="selectedTab == 'in_ex'">
        <div class="grid grid-cols-2 my-auto">
          <div class="my-auto">Taxes <span class="text-xs text-muted-foreground">({{
            formatPrice(params?.taxAnnualAmt ?? 0)
          }}/yr per listing)</span>
          </div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('tax_amount_per_month')" align='right'
              v-model="formFlip.tax_amount_per_month" type="price" />
          </div>
          <div class="my-auto">Insurance</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('insurance')" align='right' v-model="formFlip.insurance" type="price" />
          </div>
          <div class="my-auto">Utilities</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('utilities')" align='right' v-model="formFlip.utilities" type="price" />
          </div>

          <div class="my-auto">Repairs/Maint</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('repair_maintain')" align='right' v-model="formFlip.repair_maintain"
              type="price" />
          </div>
          <div class="my-auto">Landscaping</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('landscaping')" align='right' v-model="formFlip.landscaping"
              type="price" />
          </div>
          <div class="my-auto">Misc</div>
          <div class="my-auto flex">
            <DollarIcon :value="false" />
            <InputNumber @change="markManual('misc')" align='right' v-model="formFlip.misc" type="price" />
          </div>
          <div class="my-auto">Total Expenses</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPrice(formFlip.total_expenses, 2) }}
          </div>
          <div class="my-auto">Monthly Debt Service</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPrice(formFlip.monthly_payment, 2) }}
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">Monthly Carry</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPrice(formFlip.monthly_carry, 2) }}
          </div>
          <div class="col-span-2 border-b-4 my-3 "></div>
          <div class="my-auto">&nbsp;&nbsp;&nbsp;&nbsp;Hold Duration <span class="text-xs text-muted-foreground">(in
              days)</span>
          </div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formFlip.hold_amount, 2) }}</div>
            <div class="flex-2">
              <InputNumber @change="markManual('hold_duration')" align='right' class="max-w-[100px]"
                v-model="formFlip.hold_duration" />
            </div>
          </div>
          <div class="col-span-2 border-b-4 my-3 ml-3"></div>
          <div class="my-auto">&nbsp;&nbsp;&nbsp;&nbsp;Commissions</div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formFlip.commissions_amount, 2) }}</div>
            <div class="flex-2">
              <InputNumber step="0.01" @change="markManual('commissions')" align='right' class="max-w-[100px]"
                v-model="formFlip.commissions" type="percentage" />
            </div>
          </div>
          <div class="my-auto">&nbsp;&nbsp;&nbsp;&nbsp;Transfer Tax</div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formFlip.transfer_tax_amount, 2) }}</div>
            <div class="flex-2">
              <InputNumber step="0.01" @change="markManual('transfer_tax')" align='right' class="max-w-[100px]"
                v-model="formFlip.transfer_tax" type="percentage" />
            </div>
          </div>
          <div class="my-auto">&nbsp;&nbsp;&nbsp;&nbsp;Concessions</div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formFlip.concessions_amount, 2) }}</div>
            <div class="flex-2">
              <InputNumber step="0.01" @change="markManual('concessions')" align='right' class="max-w-[100px]"
                v-model="formFlip.concessions" type="percentage" />
            </div>
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">Total Sale Debits</div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formFlip.total_sale_debits) }}</div>
          </div>
        </div>
      </div>
      <div class="" v-if="selectedTab == 'roi'">

        <div class="mx-auto flex justify-around my-8">
          <div class="flex-1 max-w-[100px] relative py-4">
            <div
              class="absolute w-full top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xs text-center font-medium">
              Cash on Cash
            </div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xl">
              {{ formatPercent(formFlip.cash_on_cash) }}
            </div>
            <Pie :data="data" :options="options" />
          </div>
          <div class="flex-1 max-w-[100px] relative py-4">
            <div
              class="absolute w-full top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xs text-center font-medium">
              ROI
            </div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xl">
              {{ formatPercent(formFlip.roi) }}
            </div>
            <Pie :data="data2" :options="options" />
          </div>
          <div class="flex-1 max-w-[100px] relative py-4">
            <div
              class="absolute w-full top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xs text-center font-medium">
              Annualized ROI
            </div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xl">
              {{ formatPercent(formFlip.annualized_roi) }}
            </div>
            <Pie :data="data3" :options="options" />
          </div>
        </div>
        <div class="grid grid-cols-2">
          <div class="my-2 ">Sales Prices</div>
          <div class="my-2  text-right">{{ formatPrice(formFlip.arv) }}</div>
          <div class="my-2">&nbsp;&nbsp;&nbsp;&nbsp;Purchase Price</div>
          <div class="my-2 text-right">{{ formatPrice(formFlip.purchase_price) }}</div>
          <div class="my-2">&nbsp;&nbsp;&nbsp;&nbsp;Total Cost</div>
          <div class="my-2 text-right">{{ formatPrice(formFlip.total_cost) }}</div>
          <div class="my-2">&nbsp;&nbsp;&nbsp;&nbsp;Total Debit <span
              class="text-xs text-muted-foreground">(Sale)</span></div>
          <div class="my-2 text-right">{{ formatPrice(formFlip.total_sale_debits) }}</div>
          <div class="col-span-2 border-b-4 my-3"></div>
          <div class="my-2 1 font-medium">Flip Profit</div>
          <div class="my-2 text-right font-medium">{{ formatPrice(formFlip.flip_profit) }}</div>
        </div>
        <div class="grid grid-cols-1 py-3">
          <div class="bg-primary/30 rounded-lg shadow-sm grid grid-cols-1 py-3 px-3 gap-2">
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon(formFlip.flip_profit > 30000)" class="mr-1 size-5 my-auto"
                :class="getCalPosNegTextColor(formFlip.flip_profit > 30000)" />
              Net Profit <span class="mx-1" v-html="getCalPosNegText(formFlip.flip_profit > 30000)"></span> $30,000
            </div>
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon(formFlip.cash_on_cash > 0.1)" class="mr-1 size-5 my-auto"
                :class="getCalPosNegTextColor(formFlip.cash_on_cash > 0.1)" />
              Cash on Cash <span class="mx-1" v-html="getCalPosNegText(formFlip.cash_on_cash > 0.1)"></span> 10%
            </div>
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon(!formFlip.total_cash_needed > 50000)"
                class="mr-1 size-5 my-auto" :class="getCalPosNegTextColor(!formFlip.total_cash_needed > 50000)" />
              Cash Needed <span v-if="formFlip.total_cash_needed > 50000" class="text-red-600">&nbsp;more
                than&nbsp;</span>
              <span v-if="!formFlip.total_cash_needed > 50000" class="text-green-600">&nbsp;less than&nbsp;</span>
              $50,000
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped></style>

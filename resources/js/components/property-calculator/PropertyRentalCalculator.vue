<script setup lang="ts">
import {Pie} from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement, ChartData
} from 'chart.js'

import {useForm} from "@inertiajs/vue3";
import {
  getCalPosNegIcon,
  getCalPosNegText,
  DollarIcon,
  calculateLoan, calculatorParams, getCalPosNegTextColor
} from "@/components/property-calculator";
import {ref, watch} from "vue";
import {Icon} from "@iconify/vue";
import {InputNumber} from "@/components/ui/input-number";
import { useNumber} from "@/composables/useFormat";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
  DropdownMenuItem,
  DropdownMenuGroup
} from "@/components/ui/dropdown-menu";

import Button from "@/components/ui/button/Button.vue";
import RequestShowingDialog from "@/components/property-dialogs/RequestShowingDialog.vue";
import {DBApiPropertyFull} from "@/types/DBApi";
import InstantOfferDialog from "@/components/property-dialogs/InstantOfferDialog.vue";


const {formatPrice, formatPercent} = useNumber()
// const {formatDate} = useDateFormat()
// const {formatToCapitalizeEachWord,} = useTextFormat()

const props = defineProps<{
  propertyData: DBApiPropertyFull | null;
  params: calculatorParams | null;
  instantOffer: false,
  requestAShowing: false,
  editAllowed: false,
}>()

const manualOverrides = ref<Set<string>>(new Set())

const formRent = useForm({

  // assumptions
  purchase_price: props.params?.listPrice ?? 0,// props.params.ListPrice
  renovations: props.params?.sqft ? props.params.sqft * 10 : 0, // props.params.sqft * 10
  closing_cost: 0, // purchase_price * 0.02
  points_percentage: 0, //
  points_amount: 0, // points_amount * mortgage_amount
  total_into_deal: 0, // purchase_price + renovations + closing_cost + points_amount;
  mortgage_amount: 0, // (purchase_price + renovations) * 0.7
  purchase_plus_rehab: 0, // (purchase_price + renovations)
  mortgage_rental_percentage: 0, // mortgage_amount / purchase_plus_rehab
  interest_rate: 7, // 7%
  mortgage_term: 360, // 360
  monthly_payment: 0, // calculateLoan.monthlyPayment [mortgage_amount, interest_rate, mortgage_term, 0]
  avm: props.params?.avm ?? 0, // props.params.avm
  total_interest_per_month: 0, // calculateLoan.totalInterest / mortgage_term

  // common
  cashflow_per_year: 0,// cashflow * 12
  noi_rent_monthly: 0, // total_income - total_expenses
  noi_rent_annual: 0, // noi_rent_monthly * 12
  coc_rent: 0, // cashflow_per_year / total_into_deal


  // income/expenses
  gross_rent: props.params?.avgRent ?? 0, //props.params.avgRent
  other_income: 0, //
  total_income: 0, // gross_rent + other_income
  tax_amount_per_month: props.params?.taxAnnualAmt ? Number((props.params.taxAnnualAmt / 12)?.toFixed(2)) : 0, // props.params.taxAnnualAmt / 12
  insurance: props.params?.arv ? Number((props.params.arv * 0.00038)?.toFixed(2)) : 0, // props.params.arv * 0.00038
  vacancy: 0, // gross_rent * 0.05
  utilities: 0, //
  management: 0, //
  cap_ex: 0, // gross_rent * 0.05
  repair_maintain: 0, // gross_rent * 0.05
  other_expenses: 0,
  expense_percentage: 0, // total_expenses / total_income
  total_expenses: 0, // tax_amount_per_month + insurance + vacancy + utilities + management + cap_ex + repair_maintain + other_expenses
  cashflow: 0, // noi_rent_monthly - monthly_payment

  //ROI
  cap_rate: 0, // noi_rent_annual / total_into_deal
  cash_into_deal: 0, // total_into_deal - mortgage_amount
  equity_at_purchase: 0, // avm - total_into_deal
  dscr: 0, // noi_rent_annual / (monthly_payment * 12)
  debt_paydown: 0, // monthly_payment - total_interest_per_month
  appreciation: 0, // avm * 0.00333333
  depreciation: 0, // total_into_deal / 330

});

const data = ref<ChartData>({
  labels: ['Cash Flow', 'Expenses', 'Mortgage'],
  datasets: [{
    label: 'ROI Rental',
    data: [0, 0, 0],
    backgroundColor: ['#f87171', '#60a5fa', '#facc15'],
  }],
});

const options = {
  responsive: true,
  cutout: '80%',
  plugins: {
    legend: {
      position: 'top',
      labels: {
        boxWidth: 10,
        boxHeight: 10,
        padding: 10,
        font: {
          size: 12
        }
      }
    },
    title: {
      display: false,
      text: 'ROI Rental'
    }
  }
}
const initialCalculation = () => {
  if (!isOverridden('closing_cost')) {
    formRent.closing_cost = formRent.purchase_price * 0.02
  }

  formRent.purchase_plus_rehab = formRent.purchase_price + formRent.renovations

  if (!isOverridden('mortgage_amount')) {
    formRent.mortgage_amount = formRent.purchase_plus_rehab * 0.7
  }

  if (!isOverridden('points_amount')) {
    formRent.points_amount = (formRent.points_percentage / 100) * formRent.mortgage_amount
  }

  formRent.total_into_deal = formRent.purchase_price + formRent.renovations + formRent.closing_cost + formRent.points_amount
  formRent.mortgage_rental_percentage = formRent.mortgage_amount / formRent.purchase_plus_rehab;
  data.value.datasets[0].data = [formRent.cashflow, formRent.total_expenses, formRent.monthly_payment]

}

watch(
    () => [formRent.purchase_price, formRent.renovations, formRent.closing_cost, formRent.points_percentage],
    () => initialCalculation(),
    {immediate: true}
)

watch(
    () => [formRent.mortgage_amount, formRent.interest_rate, formRent.mortgage_term],
    () => {
      const {monthlyPayment, totalInterest} = calculateLoan({
        loanAmount: formRent.mortgage_amount,
        interestPercent: formRent.interest_rate,
        termMonths: formRent.mortgage_term,
        totalFee: 0,
      })

      formRent.monthly_payment = monthlyPayment
      formRent.total_interest_per_month = totalInterest / formRent.mortgage_term
      formRent.mortgage_rental_percentage = formRent.mortgage_amount / formRent.purchase_plus_rehab;

      data.value.datasets[0].data = [formRent.cashflow, formRent.total_expenses, formRent.monthly_payment]
    },
    {immediate: true}
)

watch(
    () => [
      formRent.gross_rent,
      formRent.other_income,
      formRent.tax_amount_per_month,
      formRent.insurance,
      formRent.utilities,
      formRent.management,
      formRent.cap_ex,
      formRent.repair_maintain,
      formRent.other_expenses,
      formRent.monthly_payment
    ],
    () => {
      // Income
      formRent.total_income = formRent.gross_rent + formRent.other_income

      // Expenses
      formRent.vacancy = Number((formRent.gross_rent * 0.05).toFixed(2))
      formRent.cap_ex = Number((formRent.gross_rent * 0.05).toFixed(2))
      formRent.repair_maintain = Number((formRent.gross_rent * 0.05).toFixed(2))

      formRent.total_expenses =
          formRent.tax_amount_per_month +
          formRent.insurance +
          formRent.vacancy +
          formRent.utilities +
          formRent.management +
          formRent.cap_ex +
          formRent.repair_maintain +
          formRent.other_expenses

      formRent.noi_rent_monthly = formRent.total_income - formRent.total_expenses
      formRent.noi_rent_annual = formRent.noi_rent_monthly * 12
      formRent.expense_percentage = formRent.total_income
          ? formRent.total_expenses / formRent.total_income
          : 0

      formRent.cashflow = formRent.noi_rent_monthly - formRent.monthly_payment
      formRent.cashflow_per_year = formRent.cashflow * 12

      formRent.cap_rate = formRent.total_into_deal
          ? formRent.noi_rent_annual / formRent.total_into_deal
          : 0

      formRent.cash_into_deal = formRent.total_into_deal - formRent.mortgage_amount
      formRent.coc_rent = formRent.cash_into_deal
          ? formRent.cashflow_per_year / formRent.cash_into_deal
          : 0

      formRent.equity_at_purchase = formRent.avm - formRent.total_into_deal
      formRent.dscr =
          formRent.monthly_payment > 0
              ? formRent.noi_rent_annual / (formRent.monthly_payment * 12)
              : 0

      formRent.debt_paydown = formRent.monthly_payment - formRent.total_interest_per_month
      formRent.appreciation = formRent.avm * 0.00333333
      formRent.depreciation = formRent.total_into_deal / 330
      data.value.datasets[0].data = [Number(formRent.cashflow.toFixed()), Number(formRent.total_expenses.toFixed()), Number(formRent.monthly_payment.toFixed())]

    },
    {immediate: true}
)

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const requestAShowingOpen = ref<boolean>(false);
const instantOfferOpen = ref<boolean>(false);


function markManual(field: keyof typeof formRent) {
  const raw = formRent[field]
  const value = Number(raw)
  if (raw === '' || raw === null || raw === undefined || isNaN(value)) {
    formRent[field] = 0
  } else {
    formRent[field] = value
  }

  manualOverrides.value.add(field)
}

function isOverridden(field: keyof typeof formRent) {
  return manualOverrides.value.has(field)
}

const selectedTab = ref<string>('roi'); // assum, in_ex, roi
const onTabChanged = (tab: string) => {
  selectedTab.value = tab;
}

const resetDefault = () => {
  formRent.reset();
  manualOverrides.value.clear();
  initialCalculation()

}
</script>

<template>
  <div class="w-full px-2 max-w-lg mx-auto">
    <div class="tab-header">
      <div class="tab-bar flex">
        <div class="flex-1 text-center py-2 select-none flex justify-center"
             :class="selectedTab == 'assum' ? `font-semibold text-primary-strong border-b-2 border-primary` : `` "
             @click="onTabChanged('assum')">
          Purchase <br/>Assumptions
        </div>
        <div class="flex-1 text-center py-2 select-none flex justify-center"
             :class="selectedTab == 'in_ex' ? `font-semibold text-primary-strong border-b-2 border-primary` : `` "
             @click="onTabChanged('in_ex')">
          Income/<br/>Expenses
        </div>
        <div class="flex-1 text-center py-2 select-none flex justify-center"
             :class="selectedTab == 'roi' ? `font-semibold text-primary-strong border-b-2 border-primary` : `` "
             @click="onTabChanged('roi')">
          ROI<br/>Summary
        </div>
        <div class="text-center py-2 select-none flex justify-center w-fit">
          <DropdownMenu>
            <DropdownMenuTrigger as-child class="w-fit justify-center">
              <Button variant="outline">
                <Icon icon="tabler:caret-up-down" class="iconAttr"/>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent
                class="w-[160px] rounded-lg"
                :side="'bottom'"
                align="end"
                :side-offset="4"
            >
              <DropdownMenuGroup>
                <DropdownMenuItem :as-child="true">
                  <Button @click="resetDefault" class="w-full flex justify-start" variant="ghost">
                    <Icon class="size-5" icon="tabler:restore"></Icon>
                    Reset Default
                  </Button>
                </DropdownMenuItem>
                <DropdownMenuItem :as-child="true">
                  <Button class="w-full flex justify-start" variant="ghost">
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
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('purchase_price')" align='right'
                         v-model="formRent.purchase_price"
                         type="price"/>
          </div>
          <div class="my-auto">Renovations</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('renovations')" align='right' v-model="formRent.renovations"
                         type="price"/>
          </div>
          <div class="my-auto">Closing Costs</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('closing_cost')" align='right' v-model="formRent.closing_cost"
                         type="price"/>
          </div>
          <div class="my-auto">Points(%)</div>
          <div class="my-auto flex">
            <div class="my-auto flex-1 text-right">{{ formatPrice(formRent.points_amount) }}</div>
            <div class="flex-2">
              <InputNumber @input="markManual('points_percentage')" align='right' class="max-w-[100px]"
                           v-model="formRent.points_percentage" type="percentage"/>
            </div>
          </div>
          <div class="my-auto">Total Into Deal</div>
          <div class="my-auto text-right pr-8 py-2 flex justify-end">
            <DollarIcon :value="false"/>
            {{ formatPrice(formRent.total_into_deal) }}
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">Mortgage Amount</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('mortgage_amount')" align='right'
                         v-model="formRent.mortgage_amount"
                         type="price"/>
          </div>
          <div class="col-span-2 flex justify-center py-2">
            <span class="text-xs my-auto">% of purchase + rehab</span>
            <div class="">&nbsp;&nbsp;</div>
            <span class="font-medium">{{ formatPercent(formRent.mortgage_rental_percentage) }}</span>
          </div>
          <div class="my-auto">Interest Rate</div>
          <div class="my-auto">
            <InputNumber @input="markManual('interest_rate')" align='right'
                         v-model="formRent.interest_rate"
                         type="percentage"/>
          </div>
          <div class="my-auto">Mortgage Term <span class="text-xs text-muted-foreground">(Months)</span>
          </div>
          <div class="my-auto">
            <InputNumber @input="markManual('mortgage_term')" align='right'
                         v-model="formRent.mortgage_term"/>
          </div>
          <div class="my-auto">Monthly Payment</div>
          <div class="my-auto text-right pr-8 py-2">{{ formatPrice(formRent.monthly_payment, 2) }}</div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">AVM</div>
          <div class="my-auto flex">
            <DollarIcon :value="true"/>
            <InputNumber @input="markManual('avm')" align='right' v-model="formRent.avm" type="price"/>
          </div>
        </div>
      </div>
      <div class="" v-if="selectedTab == 'in_ex'">
        <div class="grid grid-cols-2 my-auto">
          <div class="my-auto">Gross Rent</div>
          <div class="my-auto flex">
            <DollarIcon :value="true"/>
            <InputNumber @input="markManual('gross_rent')" align='right' v-model="formRent.gross_rent"
                         type="price"/>
          </div>
          <div class="my-auto">Other Income</div>
          <div class="my-auto flex">
            <DollarIcon :value="true"/>
            <InputNumber @input="markManual('other_income')" align='right' v-model="formRent.other_income"
                         type="price"/>
          </div>
          <div class="my-auto">Total Income</div>
          <div class="my-auto text-right pr-8 py-2 flex justify-end">
            <DollarIcon :value="true"/>
            {{ formatPrice(formRent.total_income) }}
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">Taxes <span class="text-xs text-muted-foreground">({{
              formatPrice(params?.taxAnnualAmt)
            }}/yr per listing)</span>
          </div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('tax_amount_per_month')" align='right'
                         v-model="formRent.tax_amount_per_month" type="price"/>
          </div>
          <div class="my-auto">Insurance</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('insurance')" align='right' v-model="formRent.insurance"
                         type="price"/>
          </div>
          <div class="my-auto">Vacancy</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('vacancy')" align='right' v-model="formRent.vacancy"
                         type="price"/>
          </div>
          <div class="my-auto">Utilities</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('utilities')" align='right' v-model="formRent.utilities"
                         type="price"/>
          </div>
          <div class="my-auto">Management</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('management')" align='right' v-model="formRent.management"
                         type="price"/>
          </div>
          <div class="my-auto">CapEX</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('cap_ex')" align='right' v-model="formRent.cap_ex"
                         type="price"/>
          </div>
          <div class="my-auto">Repairs/Maint</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('repair_maintain')" align='right'
                         v-model="formRent.repair_maintain"
                         type="price"/>
          </div>
          <div class="my-auto">Other</div>
          <div class="my-auto flex">
            <DollarIcon :value="false"/>
            <InputNumber @input="markManual('other_expenses')" align='right'
                         v-model="formRent.other_expenses"
                         type="price"/>
          </div>
          <div class="my-auto">Expense %</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPercent(formRent.expense_percentage) }}
          </div>
          <div class="my-auto">Total Expenses</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPrice(formRent.total_expenses, 2) }}
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">Monthly Debt Service</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPrice(formRent.monthly_payment, 2) }}
          </div>
          <div class="col-span-2 border-b-2 my-3 "></div>
          <div class="my-auto">Cash Flow</div>
          <div class="my-auto text-right pr-8 py-2">
            {{ formatPrice(formRent.cashflow, 2) }}
          </div>
        </div>
      </div>
      <div class="" v-if="selectedTab == 'roi'">

        <div class="w-[280px] mx-auto">
          <Pie :data="data" :options="options" class=""/>
        </div>
        <div class="grid grid-cols-2">
          <div class="my-2 border-b-1">Cashflow</div>
          <div class="my-2 border-b-1 text-right font-medium"
               :class="formRent.cashflow > 0? `text-green-600` : `text-red-600`">
            {{ formatPrice(formRent.cashflow) }}/mon
          </div>
          <div class="my-2 border-b-1">Cash on Cash</div>
          <div class="my-2 border-b-1 text-right">{{ formatPercent(formRent.coc_rent, 2) }}</div>
          <div class="my-2 border-b-1">Cap Rate</div>
          <div class="my-2 border-b-1 text-right">{{ formatPercent(formRent.cap_rate, 2) }}</div>
          <div class="my-2 border-b-1">Cash Into Deal</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.cash_into_deal) }}</div>
          <div class="my-2 border-b-1">Equity At Purchase</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.equity_at_purchase) }}</div>
          <div class="my-2 border-b-1">DSCR</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.dscr, 2) }}</div>
          <div class="my-2 border-b-1">NOI</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.noi_rent_monthly) }} /mo</div>
          <div class="my-2 border-b-1">Debt Paydown</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.debt_paydown) }} /mo</div>
          <div class="my-2 border-b-1">Appreciation</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.appreciation) }} /mo</div>
          <div class="my-2 border-b-1">Depreciation</div>
          <div class="my-2 border-b-1 text-right">{{ formatPrice(formRent.depreciation) }} /mo</div>
        </div>
        <div class="grid grid-cols-1 py-3">
          <div class="bg-primary/30 rounded-lg shadow-sm grid grid-cols-1 py-3 px-3 gap-2">
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon(formRent.cashflow > 300)"
                    :class="getCalPosNegTextColor(formRent.cashflow > 300)"
                    class="mr-1 size-5 my-auto"/>
              Cash flow
              <span class="mx-1" v-html="getCalPosNegText(formRent.cashflow > 300)"></span> $300 /mo
            </div>
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon(formRent.coc_rent > 0.1)" class="mr-1 size-5 my-auto"
                    :class="getCalPosNegTextColor(formRent.coc_rent > 0.1)"/>
              Cash on cash
              <span class="mx-1" v-html="getCalPosNegText(formRent.coc_rent > 0.1)"></span> 10%
            </div>
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon(!formRent.cash_into_deal > 50000)" class="mr-1 size-5 my-auto"
                    :class="getCalPosNegTextColor(!formRent.cash_into_deal > 50000)"/>
              Cash Needed
              <span v-if="formRent.cash_into_deal > 50000" class="text-red-600">&nbsp;more than&nbsp;</span>
              <span v-if="!formRent.cash_into_deal > 50000" class="text-green-600">&nbsp;less than&nbsp;</span> $50,000
            </div>
            <div class="flex">
              <Icon :icon="'tabler:' + getCalPosNegIcon((formRent.equity_at_purchase/ formRent.purchase_price) > 0.20)"
                    class="mr-1 size-5 my-auto"
                    :class="getCalPosNegTextColor((formRent.equity_at_purchase/ formRent.purchase_price) > 0.20)"/>
              Equity
              <span class="mx-1"
                    v-html="getCalPosNegText((formRent.equity_at_purchase/ formRent.purchase_price) > 0.20)"></span> 20%
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex mt-5 gap-3">
      <Button v-if="requestAShowing" @click="requestAShowingOpen = true" class="flex-1" variant="default">Request a
        Showing
      </Button>
      <Button v-if="instantOffer" @click="instantOfferOpen = true" class="flex-1" variant="outline">Instant Offer
      </Button>
    </div>
  </div>
  <RequestShowingDialog v-if="requestAShowingOpen" :property-data="propertyData" v-model:open="requestAShowingOpen"/>
  <InstantOfferDialog v-if="instantOfferOpen" :property-data="propertyData" v-model:open="instantOfferOpen"/>
</template>

<style scoped>

</style>

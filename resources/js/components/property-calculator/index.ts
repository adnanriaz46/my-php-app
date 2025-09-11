export {default as PropertyFlipCalculator} from './PropertyFlipCalculator.vue'
export {default as PropertyRentalCalculator} from './PropertyRentalCalculator.vue'
export {default as DollarIcon} from './DollarIcon.vue';

export interface calculatorParams {
    listPrice: number;
    sqft: number,
    avm: number,
    avgRent: number,
    taxAnnualAmt: number,
    arv: number,
}

export function getCalPosNegIcon(value: boolean) {
    return (value) ? 'circle-check' : 'exclamation-circle';
}

export function getCalPosNegTextColor(value: boolean) {
    return (value) ? ' text-green-600 ' : ' text-red-600 ';
}

export function getCalPosNegText(value: boolean) {
    return (value) ? ' <span class="' + getCalPosNegTextColor(value) + '">over</span> ' : ' <span class="' + getCalPosNegTextColor(value) + '">under</span> ';
}

export function calculateLoan({
                                  loanAmount,
                                  interestPercent,
                                  termMonths,
                                  totalFee = 0
                              }: {
    loanAmount: number;
    interestPercent: number;
    termMonths: number;
    totalFee?: number;
}) {
    const monthlyInterestRate = (interestPercent / 100) / 12;

    let monthlyPayment = 0;

    if (monthlyInterestRate === 0) {
        // No interest case
        monthlyPayment = loanAmount / termMonths;
    } else {
        monthlyPayment = loanAmount * (
            monthlyInterestRate * Math.pow(1 + monthlyInterestRate, termMonths)
        ) / (
            Math.pow(1 + monthlyInterestRate, termMonths) - 1
        );
    }

    const totalPayment = monthlyPayment * termMonths;
    const totalCost = totalPayment + totalFee;
    const totalInterest = totalPayment - loanAmount;

    return {
        monthlyPayment: parseFloat(monthlyPayment.toFixed(2)),
        totalPayment: parseFloat(totalPayment.toFixed(2)),
        totalCost: parseFloat(totalCost.toFixed(2)),
        totalInterest: parseFloat(totalInterest.toFixed(2))
    };
}

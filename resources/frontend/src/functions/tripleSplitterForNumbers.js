
/* eslint-disable space-before-function-paren */
export const makeTripple = {
  splitEvery3(number) {
    // if (!number.toString().includes(',')) return number
    const strVal = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    return strVal === 'NaN' ? 0 : strVal
  },
  numberWithCommas(number) {
    // if (number.toString().includes(',')) return number
    if (number !== '0' && typeof number !== 'undefined' && number != null) {
      const strVal = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
      return strVal === 'NaN' ? 0 : strVal
    } else {
      return '0'
    }
  },
  numberWithOutCommas(number) {
    // if (number == null || !number.toString().includes(',')) return number
    if (typeof number === 'undefined' || number == null) return 0
    else if (typeof number === 'string') return parseInt(number.replace(/,/g, ''), 10)
    else return number
  },
  getToman(number) {
    const noComma = this.numberWithOutCommas(number)
    const strVal = noComma.toString().slice(0, -1)
    return this.numberWithCommas(strVal)
  }
}
export function prependZeroIfSingleChar(inputString) {
  // Check if the input string has exactly one character
  if (inputString.length === 1) {
    // Prepend '0' to the string
    return '0' + inputString
  } else {
    // Return the original string unchanged
    return inputString
  }
}

export function  formatCurrency(number) {
  return new Intl.NumberFormat('fa-IR', {
    style: 'currency',
    currency: 'IRR',
  }).format(number || 0);
}

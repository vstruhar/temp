import Currencies from '../Currencies';

export default Currencies.map(i => ({value: i.code, label: i.symbol}));

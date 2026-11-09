const DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

const october30 = new Date(2026, 9, 30);
const january32 = new Date(2026, 0, 32);
const dates = [october30, january32];

for (let i = 0; i < dates.length; i++) {
  const date = dates[i];
  let formatted = `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
  let dayName = DAY_NAMES[date.getDay()];

  console.log(`${formatted} is a ${dayName}`);
}
// ASi seria el ejercicio resuelto

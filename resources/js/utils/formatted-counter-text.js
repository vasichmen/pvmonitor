export default (number, titles, locale) => {
    try {
        return handbook[locale](number, titles);
    } catch (e) {
        return titles[0];
    }
};

const handbook = {
    ru: formatRU,
    en: formatENG,
};

function formatRU(number, titles) {
    const cases = [2, 0, 1, 1, 1, 2];
    return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
}

function formatENG(number, titles) {
    return number > 1 ? titles[1] : titles[0];
}

export default () => {

    if (process.server) {
        return false;
    }

    (function (w, d, u) {
        var s = d.createElement('script'); s.async = true; s.src = u + '?' + (Date.now() / 60000 | 0);
        var h = d.getElementsByTagName('script')[0]; h.parentNode.insertBefore(s, h);
    })(window, document, 'https://cdn-ru.bitrix24.ru/b15435060/crm/site_button/loader_3_6lmioa.js');
}
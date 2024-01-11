window.dayjs.locale("pt-br");
window.dayjs.extend(window.dayjs_plugin_updateLocale);
window.dayjs.updateLocale("pt-br", {
    week: {
        dow: 1,
    },
});
window.dayjs.extend(window.dayjs_plugin_advancedFormat);
window.dayjs.extend(window.dayjs_plugin_calendar);
window.dayjs.extend(window.dayjs_plugin_customParseFormat);
window.dayjs.extend(window.dayjs_plugin_localeData);
window.dayjs.extend(window.dayjs_plugin_localizedFormat);
window.dayjs.extend(window.dayjs_plugin_isToday);
window.dayjs.extend(window.dayjs_plugin_isYesterday);
window.dayjs.extend(window.dayjs_plugin_minMax);
window.dayjs.extend(window.dayjs_plugin_duration);
window.dayjs.extend(window.dayjs_plugin_weekOfYear);
window.dayjs.extend(window.dayjs_plugin_isSameOrAfter);
window.dayjs.extend(window.dayjs_plugin_isSameOrBefore);

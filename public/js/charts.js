function T(e) {
    return getComputedStyle(document.documentElement).getPropertyValue(e);
}

const J = {
        gray: {
            300: T("--bs-chart-gray-300"),
            600: T("--bs-chart-gray-600"),
            700: T("--bs-chart-gray-700"),
            800: T("--bs-chart-gray-800"),
            900: T("--bs-chart-gray-900"),
        },
        primary: {
            100: T("--bs-chart-primary-100"),
            300: T("--bs-chart-primary-300"),
            700: T("--bs-chart-primary-700"),
        },
        black: T("--bs-dark"),
        white: T("--bs-white"),
        transparent: "transparent",
    },
    N = "Noto Sans",
    P = document.querySelectorAll('[data-toggle="chart"]'),
    z = document.querySelectorAll('[data-toggle="legend"]');
function B(e) {
    const { chart: t, tooltip: a } = e,
        o = (function (e) {
            let t = e.canvas.parentNode.querySelector("div");
            if (!t) {
                t = document.createElement("div");
                t.setAttribute("id", "chart-tooltip");
                t.setAttribute("role", "tooltip");
                t.classList.add("popover", "bs-popover-top");
                const a = document.createElement("div");
                a.classList.add("popover-arrow", "translate-middle-x");
                const o = document.createElement("div");
                o.classList.add("popover-content");
                t.appendChild(a);
                t.appendChild(o);
                e.canvas.parentNode.appendChild(t);
            }
            return t;
        })(t);
    if (0 === a.opacity) return void (o.style.visibility = "hidden");
    if (a.body) {
        const e = a.title || [],
            n = a.body.map((e) => e.lines),
            r = document.createElement("div");
        e.forEach((e) => {
            const t = document.createElement("h3");
            t.classList.add("popover-header", "text-center", "text-nowrap");
            const a = document.createTextNode(e);
            t.appendChild(a), r.appendChild(t);
        });
        const l = document.createElement("div");
        n.forEach((e, o) => {
            const r = a.labelColors[o],
                s = document.createElement("span");
            s.classList.add("popover-body-indicator"),
                (s.style.backgroundColor =
                    "line" === t.config.type &&
                    "rgba(0,0,0,0.1)" !== r.borderColor
                        ? r.borderColor
                        : r.backgroundColor);
            const c = document.createElement("div");
            c.classList.add(
                "popover-body",
                "d-flex",
                "align-items-center",
                "text-nowrap"
            ),
                c.classList.add(
                    n.length > 1
                        ? "justify-content-left"
                        : "justify-content-center"
                );
            const i = document.createTextNode(e);
            c.appendChild(s), c.appendChild(i), l.appendChild(c);
        });
        const s = o.querySelector(".popover-content");
        for (; s.firstChild; ) s.firstChild.remove();
        s.appendChild(r), s.appendChild(l);
    }
    const { offsetLeft: n, offsetTop: r } = t.canvas;
    (o.style.visibility = "visible"),
        (o.style.left = n + a.caretX + "px"),
        (o.style.top = r + a.caretY + "px"),
        (o.style.transform =
            "translateX(-50%) translateY(-100%) translateY(-1rem)");
}
function M(e) {
    const t = e.chart.scales[e.dataset.yAxisID || "y"];
    return (
        (e.chart.tooltip.dataPoints.length > 1
            ? " " + e.dataset.label + " "
            : " ") + t.options.ticks.callback.apply(t, [e.parsed.y, 0, []])
    );
}

(window.Chart.defaults.responsive = !0),
    (window.Chart.defaults.maintainAspectRatio = !1),
    (window.Chart.defaults.color = T("--bs-chart-default-color")),
    (window.Chart.defaults.font.family = N),
    (window.Chart.defaults.font.size = 13),
    (window.Chart.defaults.layout.padding = 0),
    (window.Chart.defaults.plugins.legend.display = !1),
    (window.Chart.defaults.elements.point.radius = 0),
    (window.Chart.defaults.elements.point.backgroundColor = J.primary[700]),
    (window.Chart.defaults.elements.line.tension = 0.4),
    (window.Chart.defaults.elements.line.borderWidth = 3),
    (window.Chart.defaults.elements.line.borderColor = J.primary[700]),
    (window.Chart.defaults.elements.line.backgroundColor = J.transparent),
    (window.Chart.defaults.elements.line.borderCapStyle = "rounded"),
    (window.Chart.defaults.elements.bar.backgroundColor = J.primary[700]),
    (window.Chart.defaults.elements.bar.borderWidth = 0),
    (window.Chart.defaults.elements.bar.borderRadius = 10),
    (window.Chart.defaults.elements.bar.borderSkipped = !1),
    (window.Chart.defaults.datasets.bar.maxBarThickness = 10),
    (window.Chart.defaults.elements.arc.backgroundColor = J.primary[700]),
    (window.Chart.defaults.elements.arc.borderColor = T(
        "--bs-chart-arc-border-color"
    )),
    (window.Chart.defaults.elements.arc.borderWidth = 4),
    (window.Chart.defaults.elements.arc.hoverBorderColor = T(
        "--bs-chart-arc-hover-border-color"
    )),
    (window.Chart.defaults.plugins.tooltip.enabled = !1),
    (window.Chart.defaults.plugins.tooltip.mode = "index"),
    (window.Chart.defaults.plugins.tooltip.intersect = !1),
    (window.Chart.defaults.plugins.tooltip.external = B),
    (window.Chart.defaults.plugins.tooltip.callbacks.label = M),
    (window.Chart.defaults.datasets.doughnut.cutout = "83%"),
    (window.Chart.overrides.doughnut.plugins.tooltip.callbacks.title =
        function (e) {
            return e[0].label;
        }),
    (window.Chart.overrides.doughnut.plugins.tooltip.callbacks.label =
        function (e) {
            const t = e.chart.options.plugins.tooltip.callbacks,
                a = t.beforeLabel() || "",
                o = t.afterLabel() || "";
            return a + e.formattedValue + o;
        }),
    (window.Chart.defaults.scales.linear.grid = {
        borderDash: [2],
        borderDashOffset: [2],
        color: T("--bs-chart-grid-line-color"),
        drawBorder: !1,
        drawTicks: !1,
    }),
    (window.Chart.defaults.scales.linear.ticks.beginAtZero = !0),
    (window.Chart.defaults.scales.linear.ticks.padding = 10),
    (window.Chart.defaults.scales.linear.ticks.stepSize = 10),
    (window.Chart.defaults.scales.category.grid = {
        drawBorder: !1,
        drawOnChartArea: !1,
        drawTicks: !1,
    }),
    (window.Chart.defaults.scales.category.ticks.padding = 20);

P.forEach(function (e) {
    const t = e.dataset.trigger;
    e.addEventListener(t, function () {
        !(function (e) {
            const t = e.dataset.target,
                a = e.dataset.action,
                o = parseInt(e.dataset.dataset),
                n = document.querySelector(t),
                r = window.Chart.getChart(n);
            if ("toggle" === a) {
                const e = r.data.datasets,
                    t = e.filter(function (e) {
                        return !e.hidden;
                    })[0];
                let a = e.filter(function (e) {
                    return 1e3 === e.order;
                })[0];
                if (!a) {
                    a = {};
                    for (const e in t) "_meta" !== e && (a[e] = t[e]);
                    (a.order = 1e3), (a.hidden = !0), e.push(a);
                }
                const n = e[o] === t ? a : e[o];
                for (const e in t) "_meta" !== e && (t[e] = n[e]);
                r.update();
            }
            if ("add" === a) {
                const e = r.data.datasets[o],
                    t = e.hidden;
                e.hidden = !t;
            }
            r.update();
        })(e);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    z.forEach(function (e) {
        !(function (e) {
            const t = window.Chart.getChart(e),
                a = document.createElement("div");
            t.legend.legendItems?.forEach((e) => {
                a.innerHTML += `<span class="chart-legend-item"><span class="chart-legend-indicator" style="background-color: ${e.fillStyle}"></span>${e.text}</span>`;
            });
            const o = e.dataset.target;
            document.querySelector(o).appendChild(a);
        })(e);
    });
});

window.Chart = window.Chart;

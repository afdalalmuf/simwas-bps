
gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";
gantt.config.show_grid = true;
gantt.config.grid_width = 500;
gantt.config.show_add = false; // ✅ hide "+" from task tree
gantt.config.readonly = true; // ✅ make all tasks read-only
gantt.config.columns = [{
    name: "text",
    label: "Audit",
    tree: true,
    width: '300',
    resize: true
},
{
    name: "start_date",
    label: "Mulai",
    align: "center",
    width: '*',
},
{
    name: "end_date",
    label: "Selesai",
    align: "center",
    width: '*',
    template: function (task) {
        let start = gantt.date.parseDate(task.start_date, "xml");
        let end = gantt.calculateEndDate(start, task.duration);
        return gantt.templates.date_grid(end);
    }
}
];

gantt.templates.task_class = function (start, end, task) {
    const today = new Date();
    const endDate = gantt.date.parseDate(task.end_date, "xml");
    const startDate = gantt.date.parseDate(task.start_date, "xml");

    // Normalize dates to date-only (no time)
    const startOnly = gantt.date.date_part(new Date(startDate));
    const endOnly = gantt.date.date_part(new Date(endDate));
    const todayOnly = gantt.date.date_part(today);

    if (endOnly < todayOnly) {
        return "task-past"; // Blue
    } else if (todayOnly.getTime() < startOnly.getTime()) {
        return "task-upcoming"; // Green
    }else if (todayOnly.getTime() <= endOnly.getTime()) {
        return "task-current"; // Yellow
    } 
};
gantt.attachEvent("onTaskClick", function(id, e) {
    const detailUrl = `/auditee/schedule/detail/${id}`;
    window.location.href = detailUrl;
    return true;
});

gantt.init("gantt_here");

loadGanttData($('#yearSelect').val());

var dp = new gantt.dataProcessor(window.routes.ganttDataUrl);
dp.init(gantt);
dp.setTransactionMode("REST");

function loadGanttData(year) {
    gantt.clearAll();

    const url = `${window.routes.ganttDataUrl}?year=${year}`;
    console.log("Fetching:", url);

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        })
        .then(data => {
            const taskCount = data.data.length;
            const rowHeight = 40;
            const padding = 100;
            const chartHeight = taskCount * rowHeight + padding;

            document.getElementById("gantt_here").style.height = chartHeight + "px";
            gantt.setSizes();
            gantt.parse({ data: data.data });
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("Failed to load Gantt data. Check the console.");
        });
}

// Listen for dropdown change
$('#yearSelect').on('change', function () {
    let year = $(this).val();
    const projectId = this.value;
    loadGanttData(projectId);
});

window.addEventListener("resize", function () {
    gantt.setSizes();
});

function toggleLegend() {
    const legend = document.querySelector('.gantt-legend-float');
    legend.classList.toggle('legend-open');
}
document.addEventListener("DOMContentLoaded",function(){
    const dataElement = document.getElementById("chart-data");
    const chartData = JSON.parse(dataElement.dataset.chart);

    const labels = chartData.map(item=>item.date);
    const values = chartData.map(item=>item.count);

    new Chart(document.getElementById('taskChart'),{
        type:'line',
        data:{
            labels: labels,
            datasets:[{
                label: "Task Created",
                data:values,
                borderColor:'blue',
                tension:0.4
            }]

        }
    })
})
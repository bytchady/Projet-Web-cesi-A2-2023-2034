document.addEventListener('DOMContentLoaded', function() {

    mychart1();
    mychart2();
});







function mychart1() {
        const ctx = document.getElementById('myChart1');
        const data1 = {
            labels: labels,
            datasets: [{
                label: 'Repartition des entreprises par Secteur d\'activité',
                data: values,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };
        new Chart(ctx, {
            type: 'pie',
            data: data1,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Repartition des entreprises par Secteur d\'activité'
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
}

function mychart2() {
    const ctx2 = document.getElementById('myChart2');
    const data2 = {
        labels: labels1,
        datasets: [{
            label: 'Repartition des entreprises par secteur localité',
            data: values1,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(201, 203, 207, 0.2)'
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
            ],
            borderWidth: 1
        }]
    };



    new Chart(ctx2, {
        type: 'pie',
        data: data2,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Repartition des entreprises par secteur localité'
                },
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

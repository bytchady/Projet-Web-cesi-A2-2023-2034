document.addEventListener("DOMContentLoaded", function() {
    //mychart3();
    mychart4();
})


// const ctx = document.getElementById('myChart1');
// const labels = ['2 mois', '3 mois', '6 mois', '9 mois', '12 à 17 mois', '18 mois ou +', ];
// const data = {
//     labels: labels,
//     datasets: [{
//         label: "Repartition des offres par durée de stage",
//         data: [65, 59, 80, 81, 56, 55, 40],
//         backgroundColor: [
//             'rgba(255, 99, 132, 0.2)',
//             'rgba(255, 159, 64, 0.2)',
//             'rgba(255, 205, 86, 0.2)',
//             'rgba(75, 192, 192, 0.2)',
//             'rgba(54, 162, 235, 0.2)',
//             'rgba(153, 102, 255, 0.2)',
//             'rgba(201, 203, 207, 0.2)'
//         ],
//         borderColor: [
//             'rgb(255, 99, 132)',
//             'rgb(255, 159, 64)',
//             'rgb(255, 205, 86)',
//             'rgb(75, 192, 192)',
//             'rgb(54, 162, 235)',
//             'rgb(153, 102, 255)',
//             'rgb(201, 203, 207)'
//         ],
//         borderWidth: 1
//     }]
// };
//
// const config = {
//     type: 'bar',
//     data: data,
//     options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         scales: {
//             y: {
//                 beginAtZero: true
//             }
//         }
//     },
// };
//
// new Chart(ctx, config);
//
//
function mychart2() {
    const data2 = {
        labels:  labels1,
        datasets: [{
            labels: 'Repartition des offres par compétence',
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

    const ctx2 = document.getElementById('myChart2');

    new Chart(ctx2, {
        type: 'doughnut',
        data: data2,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Repartition des offres par compétence'
                },
                legend: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function mychart3() {
    const data3 = {
        labels: labels2,
        datasets: [{
            labels: 'Repartition des offres par Localité',
            data: values2,
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
    new Chart(ctx3, {
        type: 'pie',
        data: data3,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Repartition des offres par Localité'
                },
                legend: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function mychart4() {
    const ctx3 = document.getElementById('myChart4');
    const data4 = {
        labels: labels3,
        datasets: [{
            labels: 'Repartition des offres par Promotion',
            data: values3,
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
    const ctx4 = document.getElementById('myChart4');

    new Chart(ctx4, {
        type: 'doughnut',
        data: data4,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Repartition des offres par Promotion'
                },
                legend:false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

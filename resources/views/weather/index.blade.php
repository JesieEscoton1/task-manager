<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
    }

    p,
    h5 {
        margin: 0.5rem;
    }

    .weather-update {
        width: 414px;
        height: 610px;
        display: block;
        margin: auto;
        text-align: center;
        padding: 40px 0;
        overflow: hidden;
        border-radius: 50px;
        background-image: linear-gradient(#30A2C5, #00242F);
    }

    .bar {
        display: flex;
        justify-content: space-between;
        padding: 0 40px;
    }

    .bar a {
        color: #fff;
        font-size: 20px;
    }

    .titlebar {
        line-height: 0.5rem;
        color: #fff;
    }

    .titlebar h4 {
        font-size: 40px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 2rem;
    }

    .titlebar .description {
        text-transform: uppercase;
    }

    .temperature {
        background: rgb(206, 206, 206);
        border-radius: 500px;
        width: 200px;
        height: 200px;
        text-align: center;
        display: block;
        margin: auto;
        box-shadow: 0 30px 20px #1d1d1d36;
        margin: auto;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .temperature img {
        margin-bottom: -20px;
    }

    .temperature p {
        font-size: 14px;
    }

    .temperature h2 {
        margin: 0;
        font-size: 60px;
        font-weight: 300;
    }

    .extra {
        display: flex;
        justify-content: space-around;
        color: #fff;
        padding-bottom: 30px;
    }

    .extra .col .info {
        padding-bottom: 10px;
    }

    .dataweather {
        background: #fff;
        padding: 20px;
        border-radius: 50px;
        margin-top: 20px;
        display: block;
        height: 400px;
        font-size: 14px;
        position: relative;
    }

    .dataweather .table {
        display: flex;
        justify-content: space-around;
    }

    .dataweather .table .box {
        font-size: 14px;
        border: #D4D4D4 solid 1px;
        border-radius: 20px;
        padding: 5px;
        margin: 5px;
    }

    .dataweather .table div i {
        font-size: 150%;
        color: #585858;
    }

    .dataweather .firm {
        padding-top: 20px;
        font-size: 12px;
    }
    </style>
</head>

<body style="background-color: #f3f4f4;">
    @include('layouts.sidebar')
    <div class="weather-update"></div>
</body>

</html>
<script>

    'use strict'
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    let dateObj = new Date();
    let month = monthNames[dateObj.getUTCMonth()];
    let day = dateObj.getUTCDate() + 1;
    let year = dateObj.getUTCFullYear();

    let newdate = `${month} ${day}, ${year}`;

    const app = document.querySelector('.weather-update');

    fetch(
            'https://api.openweathermap.org/data/2.5/weather?q=Philippines&appid=a6ce2cc0b7a08924a5a16ca43ee042c3&units=metric')
        .then(response => response.json())
        .then(data => {
            console.log(data)
            app.insertAdjacentHTML('afterbegin', `<div class="bar">
            <div class="center"><a href="#"><i class="fas fa-crosshairs"></i></a></div>
                <div class="search"><a href="#"><i class="fas fa-search"></i></a></div>
                    </div><div class="titlebar">
                    <p class="date">${newdate}</p>
                    <h4 class="city">${data.name}</h4>
                    <p class="description">${data.weather[0].description}</p>
                    </div>
                    <div class="temperature">
                        <img src="http://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png" />
                        <h2>${Math.round(data.main.temp)}°C</h2>
                    </div>
                    <div class="extra">
                        <div class="col">
                            <div class="info">
                                <h5>Wind Status</h5>
                                <p>${data.wind.speed}mps</p>
                            </div>
                            <div class="info">
                                <h5>Visibility</h5>
                                <p>${data.visibility} m</p>
                            </div>
                        </div>
                    <div class="col">
                        <div class="info">
                            <h5>Humidity</h5>
                            <p>${data.main.humidity}%</p>
                        </div>
                        <div class="info">
                            <h5>Air pressure</h5>
                            <p>${data.main.pressure} mph</p>
                        </div>
                </div>
            </div>`)
        });
</script>
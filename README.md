<img src="https://github.com/Markov-Andrey/budget-buddy-back/raw/master/public/images/owl_hd.png" alt="Лого проекта" height="300" width="auto">

# 🦉💰 Owl-Budget

# 🇺🇸 English Version
## Your Nest for Financial Stability!
A project for those who manage their financial flows and want pinpoint accuracy in budget planning.

## 🌟 Features
- 📜 **Receipt Conversion**: Transforming paper and electronic receipts into data processed by a neural network for subsequent loading into a database.
- 📊 **Expense Analysis**: The obtained data will accumulate and subsequently be analyzed, building expense charts by categories for the month.
- 🗓️ **Long-term Planning**: With long-term data, it's possible to plan and develop long-term family budget strategies more carefully.

## 🚧 Work in Progress
- This project is still in development, and some features may be unavailable or work incorrectly.

---

# 🇷🇺 Русская версия
## Гнездышко финансового благополучия!
Проект для тех, кто управляет своими денежными потоками и хочет ювелирной точности в планировании бюджета

## 🌟 Особенности
- 📜 **Преобразование чеков**: Превращение бумажных и электронных чеков в данные, которые обрабатываются нейросетью для последующей загрузки в базу данных.
- 📊 **Анализ расходов**: Полученные данные будут накапливаться и в последующем анализироваться, выстраивая графики расходов за месяц по категориям трат.
- 🗓️ **Долгосрочное планирование**: На долгосрочных данных можно более тщательно планировать и выстраивать долгосрочные стратегии семейного бюджета.

## 🚧 Работа в процессе
- Этот проект все еще находится в разработке, и некоторые функции могут быть недоступны или работать некорректно.

## Project setup
![Moonshine GitHub](https://avatars.githubusercontent.com/u/129834687?s=48&v=4)
Created using the Moonshine admin panel: [Moonshine GitHub](https://github.com/moonshine-software/moonshine)
### Install dependencies using Composer
```
composer install
```
### Copy the .env file
Copy the .env file and fill in the necessary environment variables
- Configure Discord bot settings.
- Set Gemini AI API key (
  API requests must be made over a VPN from a number of countries (and without the label [1])
  https://ai.google.dev/gemini-api/docs/available-regions?hl=ru#unpaid-tier-unavailable).

### Generate a Laravel application key
```
php artisan key:generate
```
### Add a new admin for the admin panel
```
php artisan moonshine:user
```
### Run migrations to create necessary database tables
```
php artisan migrate
```
### Seed the database with data
```
php artisan db:seed
```
### Start the web server
```
php artisan serve
```
### Utilize Swagger documentation for exploring the project

## Screenshots:
<img src="https://github.com/Markov-Andrey/budget-buddy-back/blob/master/public/images/1.png" alt="1" height="550" width="auto">
<img src="https://github.com/Markov-Andrey/budget-buddy-back/blob/master/public/images/3.png" alt="3" height="550" width="auto">
<img src="https://github.com/Markov-Andrey/budget-buddy-back/blob/master/public/images/4.png" alt="4" height="550" width="auto">
<img src="https://github.com/Markov-Andrey/budget-buddy-back/blob/master/public/images/5.png" alt="5" height="550" width="auto">
<img src="https://github.com/Markov-Andrey/budget-buddy-back/blob/master/public/images/6.png" alt="6" height="550" width="auto">

<div class="container">
    <div>
        Афи - личный дискорд-бот, помощник для хранения сообщений пока сервер находится в спячке.
    </div>
    <div>
        Приложение - бот подключается к чату и при запуске собирает историю чата.
        Просмотренные сообщения помечаются.
        Реагирует на имя Афи, и ключевую команду, порядок слов не важен.
    </div>

    <hr>

    <h2>Конфигурации бота</h2>
    <table>
        <tr>
            <td>Ограничение обработки</td>
            <td>25 последних сообщений из чата</td>
        </tr>
        <tr>
            <td>API Discord</td>
            <td>{{ env('DISCORD_API_URL') }}</td>
        </tr>
        <tr>
            <td>ID чата</td>
            <td>{{ env('DISCORD_CHAT_ID') }}</td>
        </tr>
    </table>

    <hr>

    <h1>Команды для обработки сообщений:</h1>
    <div class="command">
        <h2>1. Афи, чек</h2>
        <p>Можно прикрепить 1 или несколько фото чека или написать сообщение в формате, через запятую!</p>

        <p><strong>Схема</strong></p>
        <div class="example-message">
            <p>&lt;наименование&gt; (обязательно),</p>
            <p>&lt;цена&gt; руб (обязательно, ключевое слово "р", "руб", "рублей" является ключом к поиску цены в сообщении),</p>
            <p>&lt;количество&gt; (не обязательно, в случае отсутствия = 1, ключ - "шт", "штук"),</p>
            <p>&lt;вес/объем&gt; (не обязательно, в случае отсутствия = 0, ключ - "л" или "кг")</p>
        </div>

        <p><strong>Пример сообщения без чека:</strong></p>
        <div class="example-message">
            <p>Афи, чек</p>
            <p>KFC, 1 шт, 38 руб</p>
            <p>АИ 92, 15 л, 25 руб</p>
        </div>
        <p><strong>Пример сообщения с чеком:</strong></p>
        <div class="example-message">
            <p>Афи, чек</p>
            <img class="discord-image" src="/images/discord_example_image.jpg" alt="Чек">
        </div>
    </div>

    <div class="command">
        <h2>2. Афи, доход</h2>
        <p>Можно внести 1 запись со статьей дохода.</p>
        <p><strong>Допустимые категории:</strong></p>
        <ul>
            <li>Заработная плата</li>
            <li>Подработка</li>
            <li>Пассивный доход</li>
            <li>Сдача в аренду</li>
            <li>Разовый доход</li>
            <li>Продажа б/у</li>
        </ul>

        <p><strong>Схема</strong></p>
        <div class="example-message">
            <p>&lt;наименование категории&gt; (обязательно),</p>
            <p>&lt;цена&gt; руб (обязательно, ключевое слово "руб" является ключом к поиску цены в сообщении),</p>
        </div>

        <p><strong>Пример:</strong></p>
        <div class="example-message">
            <p>Афи, доход</p>
            <p>Разовый доход, 1500 руб</p>
        </div>
    </div>
</div>

<style>
    .container {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    h1 {
        margin-bottom: 20px;
    }
    h2 {
        font-size: 25px;
        font-weight: bold;
    }
    .discord-image {
        width: 250px;
    }
    .command {
        margin-bottom: 30px;
    }
    .command h2 {
        margin-bottom: 10px;
    }
    .example-message {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .example-message img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>

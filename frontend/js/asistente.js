const toggleBtn = document.getElementById("assistantToggle");

const assistantWindow =
document.getElementById("assistantWindow");

const closeBtn =
document.getElementById("closeAssistant");

const input =
document.getElementById("assistantInput");

const sendBtn =
document.getElementById("sendAssistant");

const messages =
document.getElementById("assistantMessages");

/* =========================================
   ABRIR CHAT
========================================= */

toggleBtn.addEventListener("click", () => {

    if (
        assistantWindow.style.display === "flex"
    ) {

        assistantWindow.style.display = "none";

    } else {

        assistantWindow.style.display = "flex";
    }
});

/* =========================================
   CERRAR
========================================= */

closeBtn.addEventListener("click", () => {

    assistantWindow.style.display = "none";
});

/* =========================================
   ENVIAR
========================================= */

async function sendMessage() {

    const text = input.value.trim();

    if (text === "") return;

    addUserMessage(text);

    input.value = "";

    const loadingId = addBotLoading();

    try {

const response = await fetch(
    "/backend/asistente/chat.php",
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            mensaje: text
        })
    }
);

        const data = await response.json();

        removeLoading(loadingId);

        addBotMessage(data.respuesta);

    } catch (error) {

        console.log(error);

        removeLoading(loadingId);

        addBotMessage(
            "Error al conectar con el asistente."
        );
    }
}

/* =========================================
   USER
========================================= */

function addUserMessage(text) {

    messages.innerHTML += `
    
    <div class="user-message">

        <div class="msg-content">
            ${text}
        </div>

    </div>
    
    `;

    scrollBottom();
}

/* =========================================
   BOT
========================================= */

function addBotMessage(text) {

    messages.innerHTML += `
    
    <div class="bot-message">

        <div class="msg-avatar">

            <i class="fa-solid fa-shield-halved"></i>

        </div>

        <div class="msg-content">

            ${text}

        </div>

    </div>
    
    `;

    scrollBottom();
}

/* =========================================
   LOADING
========================================= */

function addBotLoading() {

    const id = "load-" + Date.now();

    messages.innerHTML += `
    
    <div class="bot-message" id="${id}">

        <div class="msg-avatar">

            <i class="fa-solid fa-shield-halved"></i>

        </div>

        <div class="msg-content">

            Procesando consulta...

        </div>

    </div>
    
    `;

    scrollBottom();

    return id;
}

/* =========================================
   REMOVER LOADING
========================================= */

function removeLoading(id) {

    const element =
    document.getElementById(id);

    if (element) {

        element.remove();
    }
}

/* =========================================
   SCROLL
========================================= */

function scrollBottom() {

    messages.scrollTop =
        messages.scrollHeight;
}

/* =========================================
   EVENTOS
========================================= */

sendBtn.addEventListener(
    "click",
    sendMessage
);

input.addEventListener(
    "keypress",
    function (e) {

        if (e.key === "Enter") {

            sendMessage();
        }
    }
);
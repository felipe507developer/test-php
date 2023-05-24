const socket = io('https://websocket.vitae-health.com',{
    extraHeaders: {
        token_base: "token",
        sala: "clinicaE"
    },
    reconnectionAttempts: 1
});
socket.on('connect', () => {
    console.log('CONECTADDO');
});

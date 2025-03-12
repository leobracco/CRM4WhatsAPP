(function() {
    var url = "../credenciales/?event=config";
    $.getJSON(url, { format: "json" })
        .done(function(data) {
            host = data.host; // hostname or IP address
            port = data.port;
            topic = data.topic; // topic to subscribe to
            useTLS = data.secure;
            username = data.username;
            password = data.token;
            cleansession = data.session;
            path = data.path;
        });
})();
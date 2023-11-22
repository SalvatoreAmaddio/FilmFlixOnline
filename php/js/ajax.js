class Ajax 
{
    xmlhttp = new XMLHttpRequest();
    #server;
    #event;

    constructor(server) 
    {
        this.#server = server;
        this.xmlhttp.onreadystatechange = () =>
        {
            if (this.xmlhttp.readyState === XMLHttpRequest.DONE && this.xmlhttp.status === 200) 
            {
                this.#event(this.xmlhttp.responseText);
            }
        };
    }

    get server() 
    {
        return this.#server;
    }

    set on(event) 
    {
        this.#event = event;
    }

    send(params) 
    {
        this.xmlhttp.open("POST", this.#server, true);
        this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        this.xmlhttp.send(params);
    }
}

class Ajax 
{
    xmlhttp = new XMLHttpRequest();
    #where;

    constructor(where) 
    {
        this.#where = where;
    }

    set setEvent(event) 
    {
        this.xmlhttp.onload = event;
    }

    send(str) 
    {
        this.xmlhttp.open("POST", this.#where + str);
        this.xmlhttp.send();
    }
}

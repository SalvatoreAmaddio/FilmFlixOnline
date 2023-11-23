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

class AbstractForm 
{
    dataSection;
    data;
    rt;
    #server;

    constructor(server) 
    {
        this.#server = server;
        this.dataSection = document.getElementById("dataSection");
        this.data = document.getElementById("data");
        this.rt = document.getElementsByClassName("rt")[0];
    }

    get newButton() 
    {
        return this.rt.getElementsByTagName("button")[4];
    }

    send(param, evt) 
    {
        let ajax = new Ajax(this.server);
        ajax.on = evt;
        ajax.send(param);
    }

    get newButton() 
    {
        return this.rt.getElementsByTagName("button")[4];
    }

    updateRecordTracker() 
    {
        this.send("updateRecordTracker=true",
        (e)=>
        {
            this.rt.innerHTML = e;
        });
    }

    displayData(data) {}

}

class DataForm extends AbstractForm 
{
    constructor(server) 
    {
        super(server);
    }
}

class ListForm extends AbstractForm
{
    table;
    
    constructor(server) 
    {
        super(server);
        this.table = this.data.getElementsByTagName("table")[0];
        this.#onRowClickedEvent();
        this.newButton.addEventListener("click",
        (e)=>
        {
            this.send("newRecord=true",
            (e)=>
            {
                location.href = e;
            });
        });
    }

    get rows() 
    {
        return this.table.children[0].children;
    }

    get rowCount() 
    {
        return this.rows.length;
    }

    #onRowClickedEvent() 
    {
        for(let i = 1 ; i < this.rowCount; i++) 
        {
            this.rows[i].addEventListener("click", (e)=>this.#rowClicked(e));
        }
    }

    displayData(data) 
    {
        this.table.innerHTML = data;
        this.#onRowClickedEvent();
        this.updateRecordTracker();
    }

    #rowClicked(e) 
    {
        let el = e.target;
        let parentNode = "";
        let clickedRow;
        let id;

        while(true) 
        {
            el = el.parentNode;
            parentNode = el;

            if (parentNode.tagName=="TR") 
            {
                clickedRow = parentNode;
                id = clickedRow.getAttribute("value");
                break;
            }
        }

        let param = "selectedID=" + id;
        let isButton = e.target.className=="editButton";
        if (isButton) 
        {
            param = param + "&amend=true";
        }

        this.send(param,
        (e)=>
        {
            if (isButton) 
            {
                location.href = e;
            } 
            else 
            {
                alert(e);
//                this.displayData(e);
            }
        });
    }

}
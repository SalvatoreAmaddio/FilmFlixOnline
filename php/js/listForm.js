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
        this.xmlhttp.open("POST", this.#server);
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
    recordTrackerLabel;

    constructor(server) 
    {
        this.#server = server;
        this.dataSection = document.getElementById("dataSection");
        this.data = document.getElementById("data");
        this.rt = document.getElementsByTagName("footer")[0];
        this.recordTrackerLabel = this.rt.getElementsByClassName("recordTrackerLabel")[0];
    }

    get goFirstButton() 
    {
        return this.rt.getElementsByTagName("button")[0];
    }

    get goPreviousButton() 
    {
        return this.rt.getElementsByTagName("button")[1];
    }

    get goNextButton() 
    {
        return this.rt.getElementsByTagName("button")[2];
    }

    get goLastButton() 
    {
        return this.rt.getElementsByTagName("button")[3];
    }

    get newButton() 
    {
        return this.rt.getElementsByTagName("button")[4];
    }

    send(param, evt) 
    {
        let ajax = new Ajax(this.#server);
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
            this.recordTrackerLabel.innerHTML = e;
        });
    }

    displayData(data) {}

}

class Form extends AbstractForm 
{
    constructor(server) 
    {
        super(server);
    }
}

class ListForm extends AbstractForm
{
    table;
    #searchBar;

    constructor(server) 
    {
        super(server);
        this.#searchBar = document.getElementById("searchBar");
        this.table = this.data.getElementsByTagName("table")[0];
        this.#onRowClickedEvent();
        this.goNextButton.addEventListener("click",(e)=>this.#sendDirection(0));
        this.goPreviousButton.addEventListener("click",(e)=>this.#sendDirection(1));
        this.goFirstButton.addEventListener("click",(e)=>this.#sendDirection(2));
        this.goLastButton.addEventListener("click",(e)=>this.#sendDirection(3));
        this.#searchBar.addEventListener("input",
        (e)=>
        {
            sessionStorage.setItem("searchValue", e.target.value);
            this.send("searchValue=" + sessionStorage.getItem("searchValue"), (e)=>this.displayData(e));
        });

        let storedSearchVal = sessionStorage.getItem("searchValue");
        if (storedSearchVal) 
            this.#searchBar.value = storedSearchVal;
    }

    #sendDirection(direction) 
    {
        let param = "";
        switch(direction) 
        {
            case 0:
                param='goNext=true';
            break;
            case 1:
                param='goPrevious=true';
            break;
            case 2:
                param='goFirst=true';
            break;
            case 3:
                param='goLast=true';
            break;
        }

        this.send(param,(e)=>this.displayData(e));
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

        this.send(param,
        (output)=>
        {
            this.displayData(output);
        });
    }

}
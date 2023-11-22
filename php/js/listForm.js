class AbstractForm 
{
    dataSection;
    data;
    rt;
    #server;

    constructor() 
    {
        this.dataSection = document.getElementById("dataSection");
        this.data = document.getElementById("data");
        this.rt = document.getElementsByClassName("rt")[0];
    }

    set server(str) 
    {
        this.#server = str;
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

        this.newButton.addEventListener("click",
        (e)=>
        {
            alert("clicked");
        });
    }

    displayData(data) {}

}

class DataForm extends AbstractForm 
{
    constructor() 
    {
        super();
    }
}

class ListForm extends AbstractForm
{
    table;
    rows;
    
    constructor() 
    {
        super();
        this.table = this.data.getElementsByTagName("table")[0];
        this.rows = this.table.children[0].children;
        this.#onRowClickedEvent();
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
        this.table.innerHTML=data;
        this.rows = this.table.children[0].children;
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

        this.send("selectedID=" + id,
        (e)=>
        {
            this.displayData(e);
        });
    }

    get rowCount() 
    {
        return this.rows.length;
    }

}
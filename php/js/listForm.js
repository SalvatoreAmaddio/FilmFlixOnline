class ListForm 
{
    dataSection;
    data;
    table;
    rows;
    rt;
    #server;

    constructor() 
    {
        this.dataSection = document.getElementById("dataSection");
        this.data = document.getElementById("data");
        this.table = this.data.getElementsByTagName("table")[0];
        this.rows = this.table.children[0].children;
        this.rt=document.getElementsByClassName("rt")[0];
        this.#onRowClickedEvent();
    }

    set server(str) 
    {
        this.#server = str;
    }

    #onRowClickedEvent() 
    {
        for(let i = 1 ; i < this.rowCount; i++) 
        {
            this.rows[i].addEventListener("click", (e)=>this.rowClicked(e));
        }
    }

    displayData(rows) 
    {
        this.table.innerHTML=rows;
        this.rows = this.table.children[0].children;
        this.#onRowClickedEvent();
        this.send("updateRecordTracker=true",
        (e)=>
        {
            this.rt.innerHTML = e;
        });
    }

    send(param, evt) 
    {
        let ajax = new Ajax(this.#server);
        ajax.on = evt;
        ajax.send(param);
    }

    rowClicked(e) 
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
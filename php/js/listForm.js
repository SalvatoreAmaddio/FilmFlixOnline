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
            let editButton = this.rows[i].getElementsByClassName("editButton")[0];
            editButton.addEventListener("click",(e)=>
            {
                this.send("amendID=" + e.target.value,
                (e)=>
                {
                    location.href = e;
                });

            });
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

        this.send("selectedID=" + id,
        (e)=>
        {
            this.displayData(e);
        });
    }

}
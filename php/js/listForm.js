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
        this.goNextButton.addEventListener("click",(e)=>this.sendDirection(0));
        this.goPreviousButton.addEventListener("click",(e)=>this.sendDirection(1));
        this.goFirstButton.addEventListener("click",(e)=>this.sendDirection(2));
        this.goLastButton.addEventListener("click",(e)=>this.sendDirection(3));
        this.goNewButton.addEventListener("click",(e)=>this.goNew());
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

    get goNewButton() 
    {
        return this.rt.getElementsByTagName("button")[4];
    }

    send(param, evt, server='') 
    {
        if (server) this.#server = server;        
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

    sendDirection(direction) 
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

    displayData(data) {}

    goNew() {}
}

class Form extends AbstractForm 
{
    constructor(server) 
    {
        super(server);
        this.saveButton.addEventListener("click",(e)=>this.save(e));
        this.deleteButton.addEventListener("click",(e)=>this.delete(e));
    }

    get recordFields() 
    {
        return this.data.getElementsByClassName("recordField");
    }

    delete(e) 
    {
        if (confirm("Are you sure you want to delete this record?") == true) 
        {
            this.send("delete=true", (e)=>
            {
                if (e) location.reload();
            });        
        }
    }

    save(e) 
    {
        let values=[];
        for(let i=0; i < this.recordFields.length; i++) 
        {
            values.push(this.recordFields[i].value);
        }
        let json = JSON.stringify(values);
        this.send("save=" + json,(e)=>
        {
            if (e) location.reload();
        });
    }

    get saveButton() 
    {
        return this.data.getElementsByClassName("saveButton")[0];
    }

    get deleteButton() 
    {
        return this.data.getElementsByClassName("deleteButton")[0];
    }

    goNew() 
    {
        this.send("newRecord=true", (output)=>{location.reload();});     
    }

    displayData(data) 
    {
        location.reload();
    }
}

class ListForm extends AbstractForm
{
    #searchBar;
    
    constructor(server) 
    {
        super(server);
        this.#searchBar = document.getElementById("searchBar");
        this.#onRowClickedEvent();
        this.#searchBar.addEventListener("input",
        (e)=>
        {
            sessionStorage.setItem("searchValue", e.target.value);
            this.send("searchValue=" + sessionStorage.getItem("searchValue"), (e)=>this.displayData(e));
        });

        let storedSearchVal = sessionStorage.getItem("searchValue");
        if (storedSearchVal) this.#searchBar.value = storedSearchVal;
        this.newButton.addEventListener("click",(e)=>this.goNew());
    }

    get newButton() 
    {
        return document.getElementById("searchPanel").children[1];
    }

    get table() 
    {
        return this.data.getElementsByTagName("table")[0];
    }

    get rows() 
    {
        return this.table.children[0].children;
    }

    get rowCount() 
    {
        return this.rows.length;
    }

    get editButtons() 
    {
        return this.table.getElementsByClassName("editButton");
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
        this.data.innerHTML = data;
        this.#onRowClickedEvent();
        this.updateRecordTracker();
    }

    #rowClicked(e) 
    {
        let elementClicked = e.target;
        let temp = elementClicked;
        let parentNode = "";
        let clickedRow;
        let id;

        while(true) 
        {
            parentNode = temp.parentNode;
            if (parentNode.tagName=="TR") 
            {
                clickedRow = parentNode;
                id = clickedRow.getAttribute("value");
                break;
            }
            temp = temp.parentNode;
        }

        let param = "selectedID=" + id;
        this.send(param,
        (output)=>
        {
            this.displayData(output);
        });

        if (elementClicked.tagName=="BUTTON") 
        {
            if (elementClicked.className.includes("editButton")) 
            {
                this.send(param,
                    (output)=>{},'/php/controller/FilmFormController.php');     
                location.href = "amend.php";
            }
        }
    }

    goNew() 
    {
        this.send("newRecord=true",
            (output)=>{},'/php/controller/FilmFormController.php');     
        location.href = "amend.php";
    }
}
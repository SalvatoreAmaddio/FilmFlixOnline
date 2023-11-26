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

class AnimationManager 
{
    constructor() 
    {
        const info = document.getElementById("info");
        const infoContent = document.getElementById("infoContent");

        document.getElementById("infoButton").addEventListener("click",(e)=>
        {
            infoContent.classList.remove("animateInfoContent2");
            info.classList.remove("animateInfo2");
            info.classList.add("animateInfo");
            infoContent.classList.add("animateInfoContent");
            info.style.display="block";
        });

        document.getElementById("closeInfoContent").addEventListener("click",(e)=>
        {
            infoContent.classList.remove("animateInfoContent");
            info.classList.remove("animateInfo");
            info.classList.add("animateInfo2");
            infoContent.classList.add("animateInfoContent2");
        });

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
        new AnimationManager();
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

    refresh() 
    {
        location.reload();
    }
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
        if (!this.checkIntegrity(values) || !this.checkMandatory(values)) 
        {
            this.refresh();
            return;
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

    checkMandatory(values) 
    {
        return true;
    }

    checkIntegrity(values) 
    {
        return true;
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

    #onRowClickedEvent() 
    {
        for(let i = 1 ; i < this.rowCount; i++)
            this.rows[i].addEventListener("click", (e)=>this.#rowClicked(e));
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

            if (elementClicked.className.includes("deleteButton")) 
            {
                this.send(param,
                    (output)=>
                    {
                        this.displayData(output);
                    });     
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

class FilmForm extends Form 
{

    checkMandatory(values) 
    {
        for(let i = 0; i < values.length; i++) 
        {
            if (values[i]==null || values[i]==false) 
            {
                alert("All fields are mandatory");
                return false;
            }
        }
        return true;
    }

    checkIntegrity(values) 
    {
        if (values[1] < 1888) 
        {
            alert("Did you know the first ever made movie was recorded in Leeds in England in 1888?");
            return false;
        }

        let currentYear = new Date().getFullYear();
        if (values[1] > currentYear) 
        {
            alert("Are you from the future?");
            return false;
        }

        if (values[2] < 1) 
        {
            alert("Please select a rating option");
            return false;
        }

        if (values[3] <= 0) 
        {
            alert("Duration cannot be less than 1");
            return false;
        }

        if (values[4] < 1) 
        {
            alert("Please select a genre option");
            return false;
        }
        return true;
    }
}
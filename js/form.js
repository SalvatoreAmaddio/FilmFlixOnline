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

class AbstractServerCaller 
{
    #server;

    constructor(server) 
    {
        this.#server = server;
    }

    get server() 
    {
        return this.#server
    }

    send(param, evt, server='') 
    {
        if (server) this.#server = server;        
        let ajax = new Ajax(this.server);
        ajax.on = evt;
        ajax.send(param);
    }
}

class RecordTracker extends AbstractServerCaller
{
    #onGoNewClicked;
    #form;

    constructor(server, form) 
    {
        super(server);
        this.#form = form;
        this.goNextButton.addEventListener("click",(e)=>this.sendDirection(0));
        this.goPreviousButton.addEventListener("click",(e)=>this.sendDirection(1));
        this.goFirstButton.addEventListener("click",(e)=>this.sendDirection(2));
        this.goLastButton.addEventListener("click",(e)=>this.sendDirection(3));
        this.goNewButton.addEventListener("click",(e)=>this.onGoNewClicked());
    }

    updateDisplayer() 
    {
        this.send("updateRecordTracker=true", (e)=>this.displayer.innerHTML = e);
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
        this.send(param,(output) => this.#form.displayData(output));
    }

    get me() 
    {
        return document.getElementsByClassName("recordTracker")[0];
    }

    get displayer() 
    {
        return this.me.getElementsByClassName("recordTrackerLabel")[0];
    }

    get goFirstButton() 
    {
        return this.me.getElementsByTagName("button")[0];
    }

    get goPreviousButton() 
    {
        return this.me.getElementsByTagName("button")[1];
    }

    get goNextButton() 
    {
        return this.me.getElementsByTagName("button")[2];
    }

    get goLastButton() 
    {
        return this.me.getElementsByTagName("button")[3];
    }

    get goNewButton() 
    {
        return this.me.getElementsByTagName("button")[4];
    }

    set onGoNewClicked(evt) 
    {
        this.#onGoNewClicked = evt;
    }

    get onGoNewClicked() 
    {
        return this.#onGoNewClicked;
    }
}

class AbstractForm extends AbstractServerCaller
{
    dataSection;
    data;
    recordTracker;
 
    constructor(server) 
    {
        super(server);
        new AnimationManager();
        this.dataSection = document.getElementById("dataSection");
        this.data = document.getElementById("data");
        this.recordTracker = new RecordTracker(server, this);
    }

    displayData(data) {}

    refresh() 
    {
        location.reload();
    }

    delete(e) 
    {
        if (confirm("Are you sure you want to delete this record?") == true) 
        {
            this.send("delete=true", (e)=>
            {
                if (e) this.displayData(e);
            });        
        }
    }
}

class Form extends AbstractForm 
{
    constructor(server) 
    {
        super(server);
        this.saveButton.addEventListener("click",(e)=>this.save(e));
        this.deleteButton.addEventListener("click",(e)=>this.delete(e));
        this.recordTracker.onGoNewClicked = ()=> {this.send("newRecord=true", (output)=>this.displayData(output))};
    }

    get recordFields() 
    {
        return this.data.getElementsByClassName("recordField");
    }

    save(e) 
    {
        let values=[];
        for(let i=0; i < this.recordFields.length; i++) 
            values.push(this.recordFields[i].value);
        
        if (!this.checkIntegrity(values) || !this.checkMandatory(values)) 
        {
            this.refresh();
            return;
        }

        let json = JSON.stringify(values);
        this.send("save=" + json,(output)=>
        {
                this.notification.children[0].innerHTML = (output=="updated") ? "Record Updated!" : "Record Created!";        
                this.notification.classList.add("animateNotification");
                let id=setInterval((e)=>
                {
                    this.notification.classList.remove("animateNotification");
                    clearInterval(id);
                    if (output="inserted") this.refresh();
                }, 2000);
        });
    }

    get notification()
    {
        return document.getElementById("notification");
    }

    get saveButton() 
    {
        return this.data.getElementsByClassName("saveButton")[0];
    }

    get deleteButton() 
    {
        return this.data.getElementsByClassName("deleteButton")[0];
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
    #goToPage;
    #talkToServer;

    constructor(server, goToPage, talkToServer) 
    {
        super(server);
        this.#goToPage = goToPage;
        this.#talkToServer = talkToServer;
        this.searchBar.addEventListener("input",(e)=>this.#sendSearchInput(e.target.value));
        this.recordTracker.onGoNewClicked = ()=> 
        {
            this.send("newRecord=true", (output)=>{},this.#talkToServer);     
            location.href = this.#goToPage;    
        }

        this.newButton.addEventListener("click", (e)=> this.recordTracker.onGoNewClicked);
        this.#onRowClickedEvent();
        this.#checkStoredSearchValue();
        this.#addDropDownOptionsListener();

        if (this.filterOption)
        {
            this.dropdownOptions.selectedIndex = this.filterOption; 
            this.#sendFilterOptions();
        }
    
        this.#scroll();
    }

    #addDropDownOptionsListener() 
    {
        this.dropdownOptions.addEventListener("change",(e)=>
        {
            let optionID = e.target.options[e.target.selectedIndex].id;
            if (this.filterOption != optionID) 
                sessionStorage.removeItem("filterValue");
            this.filterOption = optionID;
            this.#sendFilterOptions();
        });
    }

    #sendSearchInput(e) 
    {
        this.searchValue = e;
        this.send("searchValue=" + this.searchValue, (e)=>this.displayData(e));
    }

    #sendFilterValue() 
    {
        this.send("filterValue="+this.filterValue, (e)=>this.displayData(e));
    }

    #sendFilterOptions() 
    {
        this.send("filterOption="+this.filterOption, 
        (output)=>
        {
            this.filterOptionsContainer.innerHTML = output;

            if (!output) 
            {
                sessionStorage.removeItem("filterValue");
                sessionStorage.removeItem("filterOption");
                this.recordTracker.sendDirection(2);
                return;
            }
            
            if (this.filterInput) 
            {
                    this.filterInput.addEventListener("change",(e)=>
                    {
                        let filterValue = (e.target.tagName=="SELECT") ? e.target.options[e.target.selectedIndex].value : e.target.value;
                        this.filterValue = filterValue;
                        this.#sendFilterValue();
                    });            
            }

            if (this.filterValue) 
            {
                if (this.filterInput.tagName=="SELECT")
                    this.filterInput.selectedIndex = sessionStorage.getItem("filterValue");         
                else 
                    this.filterInput.value = sessionStorage.getItem("filterValue");         
                this.#sendFilterValue();
            }
        });
    }

    #checkStoredSearchValue() 
    {
        if (this.searchValue) 
        {
            this.searchBar.value = this.searchValue;
            this.#sendSearchInput(this.searchValue);
        }
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
        this.#scroll();
        this.recordTracker.updateDisplayer();
        this.backTop.children[0].addEventListener("click",(e)=>
        {
            var rows = this.table.querySelectorAll('tr');
            rows[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
            });
        });
    }

    #scroll() 
    {
        var rows = this.table.querySelectorAll('tr');
        let selectedRow = 0;
        for (let i = 0; i < rows.length; i ++) 
        {
            if (rows[i].className.includes('selectedRow')) 
                selectedRow = i;
        }

        rows[selectedRow].classList.add('active');
        rows[selectedRow].scrollIntoView({
            behavior: 'instant',
          block: 'center'
        });
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
        this.send(param, (output) =>
        {
            this.displayData(output);
        });

        if (elementClicked.tagName=="BUTTON") 
        {
            if (elementClicked.className.includes("editButton")) 
            {
                this.send(param, (output)=>{},this.#talkToServer);     
                location.href = this.#goToPage;
            }
            
            if (elementClicked.className.includes("deleteButton")) 
            {
                let id = setTimeout((e)=>
                {
                    this.delete(elementClicked);
                    clearTimeout(id);
                }, 100);
            }
        }
    }

    set searchValue(value) 
    {
        sessionStorage.setItem("searchValue", value);
    }

    get searchValue() 
    {
        return sessionStorage.getItem("searchValue");
    }

    get filterInput() 
    {
        try 
        {
            return document.getElementById("filter");
        }
        catch 
        {
            return null;
        }
    }

    get filterOption() 
    {
        return sessionStorage.getItem("filterOption");
    }

    set filterOption(value) 
    {
        sessionStorage.setItem("filterOption",value);
    }

    get filterValue() 
    {
        return sessionStorage.getItem("filterValue");
    }

    set filterValue(value) 
    {
        sessionStorage.setItem("filterValue", value);
    }

    get dropdownOptions() 
    {
        return document.getElementById('dropdownOptions');
    }

    get searchBar() 
    {
        return document.getElementById('searchBar');
    }

    get filterOptionsContainer() 
    {
        return document.getElementById('filterOptions');
    }

    get newButton() 
    {
        return document.getElementById("searchPanel").children[1];
    }

    get table() 
    {
        return this.data.getElementsByClassName("tableView")[0];
    }

    get rows() 
    {
        return this.table.children[0].children;
    }

    get rowCount() 
    {
        return this.rows.length;
    }

    get backTop() 
    {
        return document.getElementById('backTop');
    } 
}

class FilmFormList extends ListForm
{
    constructor() 
    {
        super("readIndex.php", "amend.php", "readAmend.php");

        let recordIndicators = document.getElementsByClassName("recordIndicator");
        window.addEventListener("scroll",(e)=>
        {
            if (this.searchSectionTop==0) 
            {
                this.infoButton.style.top="13.5rem";
                this.infoButton.style.left="89%";
                this.backTop.style.display="block";
            }
            else 
            {
                this.infoButton.style.top="1rem";
                this.infoButton.style.left="1rem";
                this.backTop.style.display="none";
            }
        });
    }

    get searchSectionTop() 
    {
        return document.getElementById("searchSection").getBoundingClientRect().top;
    }

    get infoButton() 
    {
        return document.getElementById("infoButton");
    }
}

class FilmForm extends Form 
{

    get table() 
    {
        return this.data.getElementsByClassName("tableView")[0];
    }

    displayData(data) 
    {
        this.table.innerHTML = data;
        this.recordTracker.updateDisplayer();
    }

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
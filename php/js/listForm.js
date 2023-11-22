class ListForm 
{
    dataSection;
    data;
    table;
    ajax;

    constructor() 
    {
        this.dataSection = document.getElementById("dataSection");
        this.data = document.getElementById("data");
        this.table = this.data.getElementsByTagName("table")[0];
    }

}
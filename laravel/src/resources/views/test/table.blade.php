<html>
    <script>
    function add() {
        let myTable = document.getElementById('myTable').getElementsByTagName('tbody')[0];

        let row = myTable.insertRow();
        let cell1 = row.insertCell(0);
        let cell2 = row.insertCell(1);
        let cell3 = row.insertCell(2);

        cell1.innerHTML = 1;
        cell2.innerHTML = 'JAHID';
        cell3.innerHTML = 23;

        row = myTable.insertRow();
        cell1 = row.insertCell(0);
        cell2 = row.insertCell(1);
        cell3 = row.insertCell(2);

        cell1.innerHTML = 2;
        cell2.innerHTML = 'HOSSAIIN';
        cell3.innerHTML = 50;
        
        row = myTable.insertRow();
        cell1 = row.insertCell(0);
        cell2 = row.insertCell(1);
        cell3 = row.insertCell(2);

        cell1.innerHTML = 3;
        cell2.innerHTML = 'HOTMAT';
        cell3.innerHTML = 50;
    }
    
    function hapus() {
    	var tableHeaderRowCount = 1;
		var table = document.getElementById('myTable');
		var rowCount = table.rows.length;
		for (var i = tableHeaderRowCount; i < rowCount; i++) {
    		table.deleteRow(tableHeaderRowCount);
		}
    }
    </script>

    <body>
        <input type="button" value="row +" onClick="add()" border=0 style='cursor:hand'>
        <input type="button" value="row -" onClick="hapus()" border=0 style='cursor:hand'>
        <input type="button" value="column +" onClick="addColumn()" border=0 style='cursor:hand'>
        <input type="button" value="column -" onClick='deleteColumn()' border=0 style='cursor:hand'>

        <table id="myTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>AGE</th>
                </tr>    
             </thead>
            <tbody></tbody>
        </table>
    </body>
</html>
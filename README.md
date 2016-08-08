Description:
Technologies used: PHPExcel, jCryption, DrawPieChart, CSS, HTML, JavaScript, jQuery, PHP and bootstrap

Parsing
System can detect wrong type of file
System can detect if file is corrupt or uploading errors
System can show error on empty files
System can detect if file extension is .xls or .xlsx but not valid file
System can show results if title sheet has no chart title on A1 index. It will name it ‘untitled’
System will work if A1 and B1 are other than ‘Count’ and ‘Name’
System will skip all non-numeric Count values
System can tell if data has no value
System will tell if there is no ‘data’ sheet
JSON data response returns after parsing

Pie Chart
Pie chart shows labels on hover
Pie chart display values in percentage
Pie chart is svg and animated
Pie chart has always unique colors for every piece
pie chart change color on hover

Encryption
Using jCryption openSSL library
OpenSSL RSA and AES for keys
Public and private key encryption
4096-bit key for encryption
Creates good confusion and diffusion
User Authentication
User authenticates against database (MySQL) record
Default user name is: demo
Default password is: admin
Creates login sessions that remains until user logs out
Access control to all pages

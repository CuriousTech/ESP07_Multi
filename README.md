# ESP07_Multi  Experiment board.  Multi-use  

I2C OLED and DHT-22 (back if regulator stays cool, otherwise extended facing top)  

BOM will have a few voltage regulator options chosen by voltage range, drop out, quiescent and support component cross compatibility.  The OLED can tax the 300mA regulator.

ADC option: Photocell or source voltage read  
IO0 could have been used for something (well, next time)  
IO12 option: button or PNP sink  
IO13 option: LED or PNP (or both)  
IO14 only raw I/O pad  
IO16 option: deepSleep, raw I/O or button.  

![Actual Device](http://www.curioustech.net/images/espmulti.png)  


iot.php - simple script for saving data from a device.  
Create a directoy named "iot" in the same directory as iot.php  
A POST to this will create an XML formatted file using 'name' as the filename.xml  
All fields will be written as XML values overwriting the older file, and appended to filename.txt as JS array data.  
A GET /iot.php?name=file will return the data in the xml file.

chart.php?name=filename will draw a chart.  This one is designed for data, temp, rh, volts.

setip.php?name=filename will create iot/filename.php as a redirector to your device.  
fwdip.php?name=myDevice will create server/iot/myDevice.php with script to forward all GET data.

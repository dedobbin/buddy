// Status: working; sending readings 80 times tested<but without really reading the current tmp or hum>
#include <Wire.h>
//ethernet
#include <SPI.h>
#include <Ethernet.h>

// constants that won't change.
const unsigned char tempSensorAddr =  (unsigned char)0b1001000; // 1001 000(1) is het adres van de temperatuursensor voor lezen, 1001 000(0) is voor lezen, maar DS1621ic doet dit automatisch 
const int DELAY_ConnectionFail = 1000, DELAY_ConnectionWait = 100;
const long DELAY_interval = 45000;
char server[] = "oege.ie.hva.nl"; // Gebruikt DNS om dit adres op te lossen
String currentRoom = "E146"; // Zal worden gelezen van de SD-kaart
IPAddress ip(192,168,0,177);
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
char *answer;

// Variables that will change:
EthernetClient client;
unsigned int currentReadingID = 0;

// function prototypes
void setup(void);
void loop(void);
int sendData(int,float,unsigned char); // verstuur de verkregen gegevens naar de oege server
void prepareEthernet(boolean);
char getData(void);
float getTemperature(void);
unsigned char getHumidity(void);// Verkrijg een percentage luchtvochtigheid
//unsigned char backupToSD(int,float,unsigned char); // Sla een backup op op de SD-kaart
//? backupFromSD(int);// Krijg één record/meting terug van de gegevens op de SD
//void eraseBackup();// Verwijder alle tot nu toe verzamelde gegevens, zodat ze worden weggegooid
void stringConcatDouble(double,unsigned int, String*);

void setup() {
  
	// SERIAL
	{
	Serial.begin(9600);
	}

	// alloceren: genoeg ruimte voor de packet die wordt ontvangen van server
	answer = (char *) malloc(256);
	
	// TEMPERATURE SENSOR
	{
	Wire.begin(); //I2C
	
	Wire.beginTransmission(tempSensorAddr);
	// temp sensor instellen om zijn Config register te beschrijven
	Wire.write(0xAC);
	// 1 shot meting doen(0000 0001)
	//delay(15);
	Wire.write(0b00000001); // werkt met 0x01, 0x00 zou betekenen dat er constant wordt gemeten
	Wire.endTransmission();
	
	// Kijken of het werkelijk gelukt is..
	Wire.beginTransmission(tempSensorAddr);
	Wire.write(0xAC);// config register
	Wire.endTransmission();
	Wire.requestFrom((int)tempSensorAddr,1,true);
	byte ans = Wire.read();
	Serial.print("nacontrole:");
	Serial.println(ans,BIN);
	}
	// HUMIDITY SENSOR
	{
	}
	
	// ETHERNET SHIELD
	{
  	prepareEthernet(true);
	}
}

void loop(){
	// temperatuur
	float temp = getTemperature();
    
	// humidity
	int hum = getHumidity();
	
	int sendStatus = sendData(currentReadingID,temp,hum);
	
	if(sendStatus!=-1){ // alleen wanneer er een connectie met de server was
		int antwoord = getData();
		Serial.print("Response from server: ");
		Serial.print(antwoord-'0');
		Serial.print('\t');
		String tekst;
    
		switch(antwoord){
			case -1:
				tekst = "-1? Helemaal fout!";
				break;
			case '0': 
				tekst = "0 = niet goed ontvangen";
				break;
			case '1':
				tekst = "1 = goed ontvangen";
				break;
			case '2':
				tekst = "2 = niet goed ontvangen + reset meting ID";
				currentReadingID = -1;
				break;
			case '3':
				tekst = "3 = goed ontvangen + reset meting ID";
				currentReadingID = -1;
				break;
			default:
				Serial.print(antwoord);
				tekst = " onbekende status response?";
		}
		
		Serial.println(tekst);
	}

	//debug
	Serial.print("Reading ");
	Serial.print(currentReadingID);
	Serial.println(" - afgerond\n");
	currentReadingID++;
  
	delay(DELAY_interval);// wacht een aangegeven tijd, hoort eigenlijk door een software interrupt te gaan.
}

int sendData(int readingID, float temp, int hum){
	byte pogingnr = 0 ;
	String content ="";
	///*
	//content.reserve(127);
	content += "sensoren_sensor_id=";
	content += currentReadingID;
	content += "&sensoren_sensor_loc=";
	content += currentRoom;
	content += "&sensoren_sensor_temp=";
	stringConcatDouble(temp,2,&content);
	content += "&sensoren_sensor_hum=";
	content += hum;	// hoort niet als teken te worden weergegeven bij een unsigned char--> cast naar unsigned int
	//*/
	Serial.print("content: (");
	Serial.print(content.length());
	Serial.print(") \"");
	Serial.print(content);
	Serial.println("\"");
	
	Serial.print("Connecting");
	while (pogingnr<10){
		Serial.print("..");

		//prepareEthernet(true);
                //delay(DELAY_ConnectionWait);

		if(pogingnr!=0)
		Serial.print(pogingnr);

		if (client.connect(server, 80)){ // Maak verbinding met de server 
		
			Serial.println("connected");
			// Make an HTTP request:
			client.println("POST /~frisw001/iot/sensors.php HTTP/1.1");
			client.println("Host: oege.ie.hva.nl");
			client.println("Connection: close");
			client.println("Content-Type: application/x-www-form-urlencoded");
			client.print("Content-Length: ");
			client.println(content.length());
			client.println();
			// Data:
			client.println(content);

			Serial.println();
			return pogingnr;//goed verzonden
		} else {
			// you didn't get a connection to the server: wacht 1 sec, probeer dan opnieuw
			delay(DELAY_ConnectionFail);
			pogingnr++;
		}
	}
	Serial.println(", Connection failed(10 tries)!");
	String str;
	//str.reserve(60); //reserves sufficient storage space to avoid memory reallocation
	// Uitgerekend krijg je meestal 100 chars in de variabele content
	str+="Opslaan op SD: ";
	str+=readingID;
	str+="\t tmp: ";
	stringConcatDouble(temp,2,&str);
	str+=", hum: ";
	str+=hum;
	str+="%";
	Serial.println(str);
    // Opslaan op SD
	// backupToSD(readingID,temp,hum);
	
    return -1;// niet verzonden
}

void prepareEthernet(boolean debug){

//	Serial.println("iets invoeren (max tien chars)");
//	while (Serial.available()==0){
//		// niets doen, wachten op input !! debug !!
//	}
	char tmp[10];
  
	Serial.readBytes(tmp,10);// leegt de .available()?
  
	if(debug)Serial.print("Maintaining ethernet... ");
	byte maintained = Ethernet.maintain();
  /*
    0: nothing happened
    1: renew failed
    2: renew success
    3: rebind fail
    4: rebind success
  */
  if(debug){
	String s;
	switch(maintained){
		case 0: s = "nothing happened";break;
		case 1: s = "renew failed";break;
		case 2: s = "renew success";break;
		case 3: s = "rebind fail";break;
		case 4: s = "rebind success";break;
		default:s = "onbekend..";
	}
	s += ": ";
	Serial.print(s);
	Serial.println(maintained);
  
  
	Serial.println("Configuring... ");
	}
	// start the Ethernet connection:
	if (Ethernet.begin(mac) == 0) {
		Serial.println("Failed to configure Ethernet using DHCP");
		// no point in carrying on, so do nothing forevermore:
		// try to congifure using IP address instead of DHCP:
		//Ethernet.begin(mac, ip);
		return;
	} else{
		if(debug)Serial.println("Configured ethernet using DHCP");
	}
	// give the Ethernet shield a second to initialize:
	delay(1000);
}

char getData(){ // nog niet goed getest
  // ontvangen
  Serial.println("\nGaat nu data ontvangen...");
  int teller =0, doubleEnter =-1;
  // teller is om de plaats waar tweemaal achter elkaar het teken '\n' wordt doorgegeven vanaf de server.
  // doubleEnter is voor tijdens het processen van de informatie
  char tempChar = 0 ;// nul
  //char answer[256];//de teruggegeven bytes
    while(teller<255){
      // if there are incoming bytes available 
      
      // check of er genoeg ruimte over is
      //if(teller>=255)
      //  break;
      
      
      // from the server, then read, print and store them:
      
      if (client.available()) {
//        char c = client.read();
//        Serial.print(c);
//        answer[teller] = c;
        //Serial.print(
		answer[teller] = client.read();//);
        
        teller++;
        //kijk waar de inhoud begint
        if(doubleEnter==-1 && tempChar=='\n' && answer[teller-1]=='\r'){ // answer[teller-1] = c
          // Wanneer het vorige karakter EN het huidige karakter '\n' is
          doubleEnter = teller+1; // +1 voor de volgende letter(die na de line break)
          //Serial.println("-----Double LineBreak(2x\\n)!-----");
          Serial.println(doubleEnter);
        }
        tempChar = answer[teller-1];
      }
    if (!client.connected()) {
      // Serial.println();
      Serial.print("disconnect");
      client.stop();
      Serial.println("ing.\n");
      break; // voor de zekerheid
    }
  }
  
  Serial.print("In totaal ");
  Serial.print(teller);
  Serial.println(" tekens in het package");
  // Nu het antwoord van de server nog ontleden(1 char; statuscode)
  
  // Eerst krijg je een 1, geen idee waarvoor, dan een line break, dan het getal(char)
  tempChar = (doubleEnter==-1)? -1: answer[doubleEnter+1+2] ;
  //free(answer);
  return tempChar;

}

float getTemperature(){
  
	Serial.print("temp{");

	Wire.beginTransmission(tempSensorAddr);
	Wire.write(0xEE);// start calculeren van temperatuur
	Wire.endTransmission();
    
	Wire.beginTransmission(tempSensorAddr);
	// Kijken wat de temperatuur is
	Wire.write(0xAA);
	Wire.endTransmission();
	Wire.requestFrom((int)tempSensorAddr,(int)2,true);
	int8_t graden = Wire.read();
	int8_t half =  Wire.read();
	float floater = ((half)? (graden + 0.5f) : graden);
	//Wire.endTransmission();
	Serial.print(floater,BIN);
	Serial.println("}");
	return floater;
}

unsigned char getHumidity(){
  
	Serial.print("hum{");
	int humidity;
//	..

	int analogPin =0, raw=0, vIn = 5;
	float vOut = 0, r1=10, r2=0, buffer=0;

	raw = analogRead(analogPin);
	vOut = (5.0 / 1023)*raw;
	buffer = (vIn/ vOut)-1;
	r2 = r1 /buffer;
        
        //translates kohm of sensor to humidity
        //humidity is meassured in high(4), mid(3), low(2), very low(1)
	if(r2 <= 10)
          humidity = 4;// relative humidity in air higher than 70%
        else if (r2 > 10 && r2 <= 100)
          humidity = 3;//70-50%
        else if (r2 > 100 && r2 <= 1000)
          humidity = 2;//50-30%
        else if (r2 > 1000)
          humidity = 1;//30%>
        else 
           humidity = 0; //invalid read
          
        
	

	Serial.print(humidity,DEC);
	//String resistnce ="";
	//stringConcatDouble(r2,3,&resistnce);
	//Serial.print(resistnce);
	Serial.println("}");
	return humidity;
}

//void softwareReset(){ // Restarts program from beginning but does not reset the peripherals and registers
//asm volatile ("  jmp 0");  
//}  

void stringConcatDouble( double val, unsigned int precision, String* str){
// prints val with number of decimal places determine by precision
// NOTE: precision is 1 followed by the number of zeros for the desired number of decimial places
// example: printDouble( 3.1415, 100); // prints 3.14 (two decimal places)

	*str += (int)val;  //prints the int part
	
	unsigned int frac;
	if(val >= 0){
		frac = (val - int(val)) * pow(10.000f,precision);
	} else {
		frac = (int(val)- val ) * pow(10.000f,precision);
	}
		
	if(frac%10 !=0)
		frac++;
		
	*str += "."; // print the decimal point
	*str += frac ;
}


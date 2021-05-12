/*
  Web client
 
 This sketch connects to a website (http://www.google.com)
 using an Arduino Wiznet Ethernet shield. 
 
 Circuit:
 * Ethernet shield attached to pins 10, 11, 12, 13
 
 created 18 Dec 2009
 by David A. Mellis
 modified 9 Apr 2012
 by Tom Igoe, based on work by Adrian McEwen
 
 */

#include <SPI.h>
#include <Ethernet.h>

// Enter a MAC address for your controller below.
// Newer Ethernet shields have a MAC address printed on a sticker on the shield
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
// if you don't want to use DNS (and reduce your sketch size)
// use the numeric IP instead of the name for the server:
//IPAddress server(74,125,232,128);  // numeric IP for Google (no DNS)
char server[] = "oege.ie.hva.nl";    // name address for Google (using DNS)

// Set the static IP address to use if the DHCP fails to assign
IPAddress ip(192,168,0,177);

// Initialize the Ethernet client library
// with the IP address and port of the server 
// that you want to connect to (port 80 is default for HTTP):
EthernetClient client;
int aantalX = 0;

// function prototypes
char getData(void);

void setup() {
 // Open serial communications and wait for port to open:
  Serial.begin(9600);
   while (!Serial) {
    ; // wait for serial port to connect. Needed for Leonardo only
  }
  doYaThing();
  doYaThing();
  doYaThing();
}

void doYaThing(){
  
  Serial.print("poging ");
  Serial.println(aantalX);
  Serial.println("Input svp...");
  while (Serial.available()==0){
    // niets doen, wachten op input
  }
  char tmp[10];
  
  Serial.readBytes(tmp,10);// leegt de .available()
  
  Serial.print("Maintaining ethernet... ");
  byte maintained = Ethernet.maintain();
  /*
    0: nothing happened
    1: renew failed
    2: renew success
    3: rebind fail
    4: rebind success
  */
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
  
   if(1){// .begin()
  Serial.println("Configuring... ");
  // start the Ethernet connection:
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Failed to configure Ethernet using DHCP");
    // no point in carrying on, so do nothing forevermore:
    // try to congifure using IP address instead of DHCP:
    //Ethernet.begin(mac, ip);
    return;
  } else{
    Serial.println("Configured ethernet using DHCP");
    }
  
  }
  // give the Ethernet shield a second to initialize:
  delay(1000);
   
  //Serial.print(Ethernet.localIP());
  Serial.println("now connecting...");

  // if you get a connection, report back via serial:
  if (client.connect(server, 80)) {
    Serial.println("connected");
    String content = "sensoren_sensor_id=";
    content += (293 + aantalX);
    content += "&sensoren_sensor_loc=E0!0&sensoren_sensor_temp=18.6&sensoren_sensor_hum=1.8";
    Serial.println(content);
    // Make a HTTP request:
    client.println("POST /~frisw001/iot/sensors.php HTTP/1.1");
    client.println("Host: oege.ie.hva.nl");
    client.println("Connection: close");
    client.println("Content-Type: application/x-www-form-urlencoded");
    client.print("Content-Length: ");
    client.println(content.length());
    client.println();
    // Data:
    client.println(content);
    
    char antwrd = getData();
    String ontvData = "De ontvangen data: ";
    ontvData += antwrd;
    Serial.println(ontvData);
  } 
  else {
    // kf you didn't get a connection to the server:
    Serial.println("connection failed");
  }
  aantalX++;
}

void loop()
{
    // do nothing forevermore:
    while(true)
      doYaThing();
}

char getData(){ // nog niet goed getest
  // ontvangen
  Serial.println("\nGaat nu data ontvangen...");
  int teller =0, doubleEnter =0;
  // teller is om de plaats waar tweemaal achter elkaar het teken '\n' wordt doorgegeven vanaf de server.
  // doubleEnter is voor tijdens het processen van de informatie
  char tempChar = 0 ;// nul
  char answer[256];//de teruggegeven bytes
    while(true){
      // if there are incoming bytes available 
      // from the server, then read, print and store them:
      
      if (client.available()) {
        char c = client.read();
        Serial.print(c);
        answer[teller] = c;
        
        teller++;
        //kijk waar de inhoud begint
        if(doubleEnter==0&&tempChar=='\n' && c=='\r'){
          // Wanneer het vorige karakter EN het huidige karakter line breaks zijn('\r''\n' achter elkaar)
          doubleEnter = teller+1; // +1 voor de volgende letter(die na de line break)
          Serial.println("-----Double LineBreak(2x\\n)!-----");
      }
        tempChar = c;
      }
    if (!client.connected()) {
      Serial.println("disconnecting.\n");
      client.stop();
      break;
    }
  }
  
  String str;
  str += "In totaal ";
  str += teller;
  str += " tekens in het package";
  Serial.println(str);
  // Nu het antwoord van de server nog ontleden(1 char; statuscode)
  
  // Eerst krijg je een 1, geen idee waarvoor, dan een line break, dan het getal(char)
  return answer[doubleEnter+1+2];

}


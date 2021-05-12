// Inclusies
#include <Wire.h>

// constants won't change. Used here to 
// set pin numbers:
const unsigned char tempSensorRead =  (unsigned char)0b1001000; // 1001 000(1) is het adres van de temperatuursensor voor lezen
const unsigned char tempSensorWrite = (unsigned char)0b1001000; // 1001 000(0) is het adres van de temperatuursensor voor schrijven

// Variables will change:
long previousMillis = 0;        // will store last time LED was updated

// the follow variables is a long because the time, measured in miliseconds,
// will quickly become a bigger number than can be stored in an int.
long interval = 2000;           // interval at which to blink (milliseconds)

// function prototype

void setup() {
  // set the digital pin as output:
  Wire.begin(); //I2C
  
  Serial.begin(9600);
    
  Wire.beginTransmission(tempSensorWrite);
  // temp sensor instellen om zijn Config register te beschrijven
  Wire.write(0xAC);
  // 1 shot meting doen(0000 0001)
  delay(15);
  Wire.write(0b00000000);
  Wire.endTransmission();
  delay(15);
  //Serial.print("tempsnsrRead = ");
  //Serial.println(tempSensorRead,BIN);
  
  // Kijken of het werkelijk gelukt is..
  Wire.beginTransmission(tempSensorRead);
  Wire.write(0xAC);
  Wire.requestFrom((int)tempSensorRead,1,true);
  byte ans = Wire.read();
  Serial.print("nacontrole:");
  Serial.println(ans,BIN);
}

void loop()
{
  /*
  bus:
  Wire.beginTransmission(tempSensorRead);
  Wire.endTransmission();
  
  */
  
  unsigned long currentMillis = millis();
 
  if(currentMillis - previousMillis > interval) {
    // save the last time you blinked the LED 
    previousMillis = currentMillis;   
    
    
    Wire.beginTransmission(tempSensorWrite);
    Wire.write(0xEE);// start calculeren van temperatuur
    Wire.endTransmission();
    Serial.print("calc,nowwait\n");
    delay(2000);
    
    int i = 0;
    
    // Is het berekenen van de temperatuur klaar?
    while (i<2){
    Wire.beginTransmission(tempSensorWrite);
    // Kijken of hij klaar is met de reading, (steeds opvragen)
    Wire.write(0xAC);
    Wire.requestFrom((int)tempSensorRead,(int)1,true);
    byte inh  ;
    Serial.print("Avail:");
    Serial.println(Wire.available());
    inh = (byte)Wire.read();
    Wire.endTransmission();
    
    Serial.print("Control reg.: ");
    Serial.print(inh,BIN);
    Serial.print(" Rdy? ");
    // bitoperatie: 
    inh >>= 7;
    Serial.println(inh,BIN);
    
    i++;
    delay(1000);
    }
    
    
    Wire.beginTransmission(tempSensorWrite);
    // Kijken wat de temperatuur is
    Wire.write(0xAA);
    Wire.endTransmission();
    Wire.requestFrom((int)tempSensorRead,(int)2,true);
    int8_t graden = Wire.read();
    int8_t half =  Wire.read();
    float floater = graden;
    floater += ((half)?0.5f:0);
    //Wire.endTransmission();
    Serial.print("Gradnbin{");
    //Serial.print(floater,BIN);
    Serial.print("} Het is nu ");
    Serial.print(floater);
    Serial.print( ((half!=0)?" en een half":""));
    Serial.print( " graden,");
    Serial.println(half);
    
  } else{
    // controleer of het verschil heel groot is(Zodat het niet meer vastloopt)
    if(currentMillis<previousMillis){
      previousMillis=0;
    }
  }
}


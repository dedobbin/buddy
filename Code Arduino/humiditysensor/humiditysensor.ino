void setup()
{
  // put your setup code here, to run once:
  
}

void loop()
{
  getHumidity();
  
}

void getHumidity(){
int analogPin =0, raw=0, vIn = 5;
float vOut = 0, r1=10, r2=0, buffer=0, humidity=0;

raw = analogRead(analogPin);
vOut = (5.0 / (1023)*raw);
buffer = (vIn/ vOut)-1;
r2 = r1 /buffer;

 if(r2 < 20)
   humidity = 20;
else if (r2 > 20 && r2 < 50) 
humidity = 50;
else if (r2 > 50 && r2 < 80)
humidity = 75;
else if (r2 >80 )
humidity = 5765;
  delay(1000);

}

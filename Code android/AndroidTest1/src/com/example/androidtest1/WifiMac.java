package com.example.androidtest1;

import java.util.List;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.wifi.ScanResult;
import android.net.wifi.WifiConfiguration;
import android.net.wifi.WifiInfo;
import android.net.wifi.WifiManager;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

public class WifiMac extends Activity {

    WifiManager wifi;
    BroadcastReceiver receiver;
    String message;
    TextView textStatus;   
    TextView wifiStatus;
    TextView macAcc;
    TextView macDev;
    TextView textview1;
    Button scan;
    private int strenght;
    private String wifiSt;
    int signalLevel;
    

    //ScanResult result;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.wifi);
       
        // Setup UI
        textStatus = (TextView) findViewById(R.id.tvWifiInfo);      
        wifiStatus = (TextView) findViewById(R.id.tvWifistrengt); 
        macAcc = (TextView) findViewById(R.id.tvMACacces); 
        macDev = (TextView) findViewById(R.id.tvMACDevice);
        textview1=(TextView) findViewById(R.id.tv1);
        scan = (Button) findViewById(R.id.bScan);
        
        // Setup WiFi
       // wifi = (WifiManager) getSystemService(Context.WIFI_SERVICE);
       

        
        
       scan.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				wifi = (WifiManager) getSystemService(Context.WIFI_SERVICE);
				//get wifi status
				WifiInfo info = wifi.getConnectionInfo();
			       
		        textStatus.setText("info van de router:  " + info.toString()+ "\n\n");
		        wifiStatus.setText("de strengt in dbm: " + info.getRssi() + "\n\n");
		        macDev.setText("Mac addres van toestel: " + info.getMacAddress() + "\n\n");
		        macAcc.setText("de mac address van de router: " +info.getBSSID() + "\n\n");
		       
		        IntentFilter i = new IntentFilter();
		        i.addAction(WifiManager.SCAN_RESULTS_AVAILABLE_ACTION);
		        List<WifiConfiguration> configs = wifi.getConfiguredNetworks();
			       for (WifiConfiguration config : configs) {
			         if(config.toString().contains("eduroam")){
			    	   textview1.append("\n\n" + config.toString());
			         }
			       
			       
		        registerReceiver(new BroadcastReceiver(){

		                @Override

		                public void onReceive(Context context, Intent intent) {
		                    // TODO Auto-generated method stub
		                   // Log.i(TAG, "opening wifimanager");
		                    
		                    textview1.append("\n\n " + wifi.getScanResults());
		                    

		                }
		            }
		        ,i);



		            wifi.startScan();

		        
		       
		       }
				
			}
		});
        
       
        

    }

    @Override
    public void onStop() {
       super.onStop();
    	if(receiver != null){
       unregisterReceiver(receiver);
       receiver=null;
    	}
    }


    }     
package com.example.androidtest1;

import java.util.ArrayList;
import java.util.List;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.wifi.ScanResult;
import android.net.wifi.WifiManager;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TabHost;
import android.widget.TextView;
import android.widget.TabHost.TabSpec;

@SuppressLint("NewApi")
public class WifiTabs extends Activity {

	TabHost th;
	TextView location;
	EditText setLokaal;
	BroadcastReceiver receiver;
	// WifiMana wm;
	WifiManager wifi;
	ArrayList<String> mList;
	private String  a,b,c, classroom;
	private String mac = "";
	private Boolean boe = false;
	
	
	Button refresh;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		System.out.println("1");
		super.onCreate(savedInstanceState);
		setContentView(R.layout.wifitest);
		wifi = (WifiManager) getSystemService(Context.WIFI_SERVICE);
		mList = new ArrayList<String>();
		init();
		//ifiInit();

		location = (TextView) findViewById(R.id.tvLocation);
		// tabsSwitch();
		refresh.setOnClickListener(new View.OnClickListener() {

			

			@Override
			public void onClick(View v) {
			 setMac("");
				wifiInit();
				boe=false;
				loopW();
				
				
			}

		});
		

	}

	public void init() {
		System.out.println("2");
		th = (TabHost) findViewById(R.id.thWifi);

		th.setup();
		TabSpec specs = th.newTabSpec("tag 1");
		specs.setContent(R.id.tab_wifi);
		specs.setIndicator("Wifi_Location");
		th.addTab(specs);
		specs = th.newTabSpec("tag 2");
		specs.setContent(R.id.tab_temp);
		specs.setIndicator("Tempratuur");
		th.addTab(specs);
		refresh = (Button) findViewById(R.id.bRefresh);
		setLokaal = (EditText) findViewById(R.id.etLokaal);
		// wm = new WifiMana();
		System.out.println("3");

	}

	public void wifiInit() {
		IntentFilter i = new IntentFilter();
		i.addAction(WifiManager.SCAN_RESULTS_AVAILABLE_ACTION);

		registerReceiver(new BroadcastReceiver() {

			@Override
			public void onReceive(Context context, Intent intent) {
				// TODO Auto-generated method stub
				// Log.i(TAG, "opening wifimanager");

				List<ScanResult> wifiList = wifi.getScanResults();
				System.out.println("6");

				for (int i = 0; i < wifiList.size(); i++) {

					ScanResult scanResult = wifiList.get(i);

					// if(scanResult.SSID.equals("eduroam")){
					a = " " + scanResult.BSSID;
					b = " " + scanResult.level;
					c = " " + scanResult.frequency;
					

					mList.add( a + b + " ");
					setMac(mac + "" + a + b + " ");
					

				}
				
				unregisterReceiver(this);
			}
		}, i);

		wifi.startScan();
	}

	@Override
	public void onStop() {
		super.onStop();
		if (receiver != null) {
			unregisterReceiver(receiver);
			receiver = null;
		}
	}
	public void loopW(){
		while(boe==false){
			
			
			if(getMac() != null && !getMac().isEmpty()){
				setText( setLokaal.getText().toString());
				System.out.println(getText());
				System.out.println(getMac());
				
				
				new PostData(getText(),getMac()).execute();
				System.out.println(" execute gedaan");
				location.append(getMac());
				boe=true;
				
			}
			}
	}
	public void setText(String classroom) {
		this.classroom=classroom;
		
		
	}
	public String getText(){
		return classroom;
	}
	public void setMac(String mac){
		this.mac=mac;
	}
	
	public String getMac(){
		return mac;
	}
}

// @Override
// public void onTabChanged(String tabId) {
// // TODO Auto-generated method stub
// switch (th.getCurrentTab()) {
// case R.id.tab_wifi:
// System.out.println("4");
// wm.resume();
//
// break;
//
// case R.id.tab_temp:
// System.out.println("5");
// wm.pause();
// break;
// }


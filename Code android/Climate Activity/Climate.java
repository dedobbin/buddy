package com.example.wtfdongs;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;

public class Climate extends Activity {

	private TextView climateTV;
	private int climateInt;
	final int MAXTEMP = 25, MINTEMP = 15;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_climate);
		
		 climateInt=20;
		 climateTV  = (TextView)findViewById(R.id.climateText);
		 climateTV.setText(climateInt+"");
		// String climateString  = tv1.getText().toString();
			}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.climate, menu);
		return true;
	}
	public void tempLower(View view){
		if (climateInt>MINTEMP){
		climateInt--;
		climateTV.setText(climateInt+"");
		Log.d(this.toString(), "temp cannot be lower");
		}
	}
	
	public void tempHigher(View view){
		if (climateInt<MAXTEMP){
		climateInt++;
		climateTV.setText(climateInt+"");
		Log.d(this.toString(), "temp cannot be lower");
		}
		
	}
	public void postData(){
		//should send data in keer in kwartier
	}

}

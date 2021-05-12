package com.example.buddy;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;

public class Climate extends Activity {

	private TextView climateTV;
	private TextView humTV;
	private int climateInt;
	final int MAXTEMP = 25, MINTEMP = 15;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_climate);

		climateInt = Storage.getPrefTemp();
		//screen elements
		Storage.setPrefTemp(20);
		climateTV = (TextView) findViewById(R.id.tvclimateText);
		climateTV.setText(climateInt + "");
		
		humTV = (TextView) findViewById(R.id.tvHumText);
		updateHumText(Storage.getPrefHum());
		// String climateString = tv1.getText().toString();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.main, menu);
		return super.onCreateOptionsMenu(menu);
	}

	public void buttonClicked(View view) {
		switch (view.getId()) {

		case R.id.bclimateLower:
			if (climateInt > MINTEMP) {
				climateInt--;
				climateTV.setText(climateInt + "");
				Storage.setPrefTemp(climateInt);
				Log.d(this.toString(), "temp cannot be lower");
			}
			break;

		case R.id.bclimateHigher:
			if (climateInt < MAXTEMP) {
				climateInt++;
				climateTV.setText(climateInt + "");
				Storage.setPrefTemp(climateInt);
				Log.d(this.toString(), "temp cannot be higher");

			}
			break;

		case R.id.bHumidityHigher:
			if (Storage.getPrefHum() < 4){
				Storage.setPrefHum(Storage.getPrefHum() + 1);
			}
			updateHumText(Storage.getPrefHum());
			
			break;
		case R.id.bHumidityLower:
			if (Storage.getPrefHum() > 1){
				Storage.setPrefHum(Storage.getPrefHum() - 1);
			Log.d(this.toString(), "hum cannot be lower");
			}
			updateHumText(Storage.getPrefHum());
			break;

		}

	}
public void updateHumText(int newHum){
	Log.d("natuurlijk", "het niet in een keer");
	switch(newHum){
	case 1:
		humTV.setText("low");
		break;
	case 2:
		humTV.setText("middle");
		break;
	case 3:
		humTV.setText("high");
		break;	
	case 4:
		humTV.setText("very high");
		break;	
	default:
	}
}
}

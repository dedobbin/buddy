package com.example.androidtest1;

import java.io.ObjectOutputStream.PutField;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.RadioGroup.OnCheckedChangeListener;
import android.widget.RadioGroup;
import android.widget.TextView;

public class OpenedClass extends Activity implements OnClickListener, OnCheckedChangeListener
		 {
	TextView question, test;
	Button returnData;
	RadioGroup selectionList;
	String gotBread, setData;
	

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.send);
		intialize();
		
		SharedPreferences getData = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
		String et = getData.getString("name", "Michael is...");
		String values = getData.getString("list", "4");
		if(values.contentEquals("1")){
			question.setText(et);
		}
		
		//Bundle getBasket = getIntent().getExtras();
		//gotBread= getBasket.getString("Key");
		//question.setText(gotBread);
		
	}

	private void intialize() {
		// TODO Auto-generated method stub
		question = (TextView) findViewById(R.id.tvQuestion);
		test = (TextView) findViewById(R.id.tvText);
		returnData = (Button) findViewById(R.id.bReturn);
		returnData.setOnClickListener(this);
		selectionList = (RadioGroup) findViewById(R.id.rgAnswers);
		selectionList.setOnCheckedChangeListener(this);

	}

	@Override
	public void onClick(View v) {
		// TODO Auto-generated method stub
			Intent person = new Intent();
			Bundle backpack= new Bundle();
			backpack.putString("answer", setData);
			person.putExtras(backpack);
			setResult(RESULT_OK, person);
			finish();
	}

	@Override
	public void onCheckedChanged(RadioGroup group, int checkedId) {
		// TODO Auto-generated method stub
		switch (checkedId) {
		case R.id.rCrazy:
			setData = "probably right!!";
			
			break;
		case R.id.rSexy:
			setData = "Yeah!!";
			
			break;
		case R.id.rBoth:
			setData = "Spot On!!";
			
			break;

		}
		test.setText(setData);
	}

}

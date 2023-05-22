package com.example.apphw2

import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.text.method.ScrollingMovementMethod
import android.view.View
import android.view.WindowManager
import android.view.animation.AlphaAnimation
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.TextView
import okhttp3.*
import okhttp3.HttpUrl.Companion.toHttpUrlOrNull
import org.json.JSONObject
import java.io.IOException
import android.os.Handler
import android.view.MotionEvent
import android.view.inputmethod.InputMethodManager
import android.widget.Toast


class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        //window.setSoftInputMode(WindowManager.LayoutParams.)
//<-----------點桌面鍵盤消失------------S>
        // 獲取最外層的 View
        val rootView: View = findViewById(android.R.id.content)

        // 設置畫面點擊事件監聽器
        rootView.setOnTouchListener { v, event ->
            // 判斷點擊事件的類型
            if (event.action == MotionEvent.ACTION_DOWN) {
                // 獲取輸入法管理器
                val inputMethodManager =
                    getSystemService(INPUT_METHOD_SERVICE) as InputMethodManager
                // 隱藏鍵盤
                inputMethodManager.hideSoftInputFromWindow(v.windowToken, 0)
            }
            false
        }
//<-----------點桌面鍵盤消失------------E>

        val yearmonth = findViewById<EditText>(R.id.YearMonth)
        val number = findViewById<EditText>(R.id.InvoiceNumber)
        val Check = findViewById<Button>(R.id.CheckButton)
        val Show = findViewById<Button>(R.id.ShowButton)
        val Heart = findViewById<ImageView>(R.id.Heart)
        val Star = findViewById<ImageView>(R.id.Star)
        val Error = findViewById<ImageView>(R.id.Error)

        Show.setOnClickListener {

            if (yearmonth.text.isNotEmpty()){
            val postyearmonth = yearmonth.text.toString()

            val parts = postyearmonth.split("-");//分割yearmonth
            val year = parts[0]
            val month = parts[1]


            //----------------
            val client = OkHttpClient()


            val urlBuilder = "https://fangbibi.lionfree.net/app/testread.php".toHttpUrlOrNull()
                ?.newBuilder()
                ?.addQueryParameter("month", "${month}")
                ?.addQueryParameter("year", "${year}")


            val url = urlBuilder?.build().toString()

            val request = Request.Builder()
                .url(url)
                .build()
            println(request.url.toString())
            client.newCall(request).enqueue(object : Callback {
                override fun onFailure(call: Call, e: IOException) {
                    e.printStackTrace()
                }

                override fun onResponse(call: Call, response: Response) {

                    if (response.isSuccessful) {
                        val responseBody = response.body?.string()
                        println("-----${responseBody}")

                        val jsonObject = JSONObject(responseBody)
                        val dataArray = jsonObject.getJSONArray("data")


                        runOnUiThread {
                            //直接將內容回傳給id名稱為TextView
                            val textView = findViewById<TextView>(R.id.Information)
                            textView.setMovementMethod(ScrollingMovementMethod.getInstance());
                            //.text = "${year} - ${month}期 \n 開獎號碼   獎項"
                            if (dataArray.length() == 0) {
                                textView.text = "查無資料，發票還未開獎或已過期6月以上"
                            } else {
                                textView.text = "${year} - ${month}期 \n 開獎號碼        獎項 \n"
                                for (i in 0 until dataArray.length()) {
                                    val chatObject = dataArray.getJSONObject(i)
                                    val cid = chatObject.getString("cid")
                                    val cmon = chatObject.getInt("cmon")
                                    val cyear = chatObject.getInt("cyear")
                                    val cprice = chatObject.getString("cprice")
                                    val cdate = chatObject.getString("cdate")

                                    //textView.append("${cmon} - ${cyear}\n")
                                    textView.append("${cid}  -  ${cprice}\n")
                                    //textView.append("${cprice}  \n")
                                    //textView.append("${cdate}  \n")


                                    // 顯示愛心 ImageView
                                    Heart.visibility = View.VISIBLE

                                    // 定義淡入淡出動畫
                                    val animation = AlphaAnimation(1.0f, 0.0f)
                                    animation.duration = 2000 // 2 秒
                                    Heart.startAnimation(animation)

                                    // 使用 Handler 在 2 秒後將愛心 ImageView 設置為不可見
                                    Handler().postDelayed({
                                        Heart.visibility = View.INVISIBLE
                                    }, 2000)
                                }
                            }
                        }
                    } else {
                        println("Request failed")
                        runOnUiThread {
                            //直接將內容回傳給id名稱為TextView
                            findViewById<TextView>(R.id.textView).text = "資料錯誤"
                        }

                    }
                }
            })
        }
            else{
                Toast.makeText(this,"請輸入資料",Toast.LENGTH_LONG).show()
                Error.visibility = View.VISIBLE

                // 定義淡入淡出動畫
                val animation = AlphaAnimation(1.0f, 0.0f)
                animation.duration = 2000 // 2 秒
                Error.startAnimation(animation)

                // 使用 Handler 在 2 秒後將愛心 ImageView 設置為不可見
                Handler().postDelayed({
                    Error.visibility = View.INVISIBLE
                }, 2000)

            }
        }
//ShowButton End
//Check Button Start
        Check.setOnClickListener {
        if((yearmonth.text.isNotEmpty()) and (number.text.isNotEmpty())){

            val textView = findViewById<TextView>(R.id.Information)
            val vid = number.text.toString()
            textView.text = ""
            println("cid-------${vid}")
            val postyearmonth = yearmonth.text.toString()
            //println("year.month-------${postyearmonth}")
            //val postnumber = number.text.toString()
            //println("number-------${postnumber}")
            println("1")
            val parts = postyearmonth.split("-");//分割yearmonth
            val year = parts[0]
            val month = parts[1]

            //----------------
            val clientPost = OkHttpClient()
            val requestBody = FormBody.Builder()
                .add("vid", "${vid}")
                .add("vmon", "${month}")
                .add("vyear", "${year}")
                .build()
            val requestPost = Request.Builder()
                .url("https://fangbibi.lionfree.net/app/testinsert.php")
                .post(requestBody)
                .build()

            clientPost.newCall(requestPost).enqueue(object : Callback {
                override fun onFailure(call: Call, e: IOException) {
                    e.printStackTrace()
                }

                override fun onResponse(call: Call, response: Response) {
                    val responseBody = response.body?.string()
                    // Handle the response body
                    println("POST-------${responseBody}")
                    //textView.append("${responseBody}")
                    runOnUiThread {
                        textView.text = responseBody?.replace("<br>", "\n")

                        // 顯示愛心 ImageView
                        Star.visibility = View.VISIBLE

                        // 定義淡入淡出動畫
                        val animation = AlphaAnimation(1.0f, 0.0f)
                        animation.duration = 2000 // 2 秒
                        Star.startAnimation(animation)

                        // 使用 Handler 在 2 秒後將愛心 ImageView 設置為不可見
                        Handler().postDelayed({
                            Star.visibility = View.INVISIBLE
                        }, 2000)
                    }
                }
            })


        }
            else{
                Toast.makeText(this,"請輸入資料",Toast.LENGTH_LONG).show()
                Error.visibility = View.VISIBLE

                // 定義淡入淡出動畫
                val animation = AlphaAnimation(1.0f, 0.0f)
                animation.duration = 2000 // 2 秒
                Error.startAnimation(animation)

                // 使用 Handler 在 2 秒後將愛心 ImageView 設置為不可見
                Handler().postDelayed({
                    Error.visibility = View.INVISIBLE
                }, 2000)

            }

        }
    //Show Button End


    }



    }






const express = require("express");
const mysql = require("mysql");
const app = express();

const db = mysql.createConnection({
  host: "localhost",
  user: "admin",
  password: "123456",
  database: "moodle",
});

app.use(express.json());

app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "GET,POST,PUT,PATCH,DELETE");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type, Authorization");
  next();
});

//--Serhat
app.post("/obshandle", (req, res) => {
  var currentTimeInMilliseconds = Date.now();
  let post = {};
  post["id"] = currentTimeInMilliseconds;
  post["examid"] = req.body["examid"];
  post["email"] = req.body["userEmail"];
  post["reg_date"] = currentTimeInMilliseconds.toString();
  post["state"] = req.body["state"];
  let sql = "INSERT INTO mdl_block_examsobs SET ?";
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    console.log("The solution is: ", results);
  });
  res.sendStatus(200);
});
app.post("/kahootquestionsreturn", (req, res) => {
  let post = {};
  post["kahootid"] = req.body["kahootid"];
  console.log(req.body["kahootid"]);
  let resBody = {};
  let sql = `SELECT questionid,question,a,b,c,d FROM moodle.mdl_local_kahootquestions WHERE kahootid=${req.body["kahootid"]}`;
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    resBody["result"] = JSON.parse(JSON.stringify(results));
    console.log("The solution is: ", JSON.parse(JSON.stringify(results)));
    res.send(resBody);
  });
});
app.post("/kahootanswer", (req, res) => {
  var currentTimeInMilliseconds = Date.now();
  let post = {};
  post["id"] = currentTimeInMilliseconds;
  post["kahootid"] = req.body["kahootid"];
  post["date"] = currentTimeInMilliseconds.toString();
  post["answer"] = req.body["answer"].toUpperCase();
  post["email"] = req.body["email"];
  post["questionid"] = req.body["questionid"];
  let sql = "INSERT INTO mdl_local_kahootanswers SET ?";
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    console.log("The solution is: ", results);
  });
  res.sendStatus(200);
});
app.post("/kahootscore", async (req, res) => {
  let kahootid = req.body["kahootid"];
  let email = req.body["email"];
  let post = {};
  let resBody = {};
  let sql = `SELECT count(email) FROM mdl_local_kahootanswers as A INNER JOIN mdl_local_kahootquestions as B ON  B.kahootid=A.kahootid and A.questionid=B.questionid and A.answer=B.answer where A.kahootid="${kahootid}" and A.email="${email}";`;
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    console.log(JSON.parse(JSON.stringify(results))[0]["count(email)"]);
    resBody["count"] = JSON.parse(JSON.stringify(results))[0]["count(email)"];
    console.log(resBody);
    res.send(resBody);
  });
});
app.post("/kahootid", async (req, res) => {
  let kahootid = req.body["kahootid"];
  let post = {};
  let resBody = {};
  let sql = `SELECT COUNT(B.id) FROM mdl_local_kahootquestions as B WHERE B.kahootid="${kahootid}"`;
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    if (JSON.parse(JSON.stringify(results))[0]["COUNT(B.id)"] == 0)
      resBody["status"] = "false";
    else resBody["status"] = "true";
    res.send(resBody);
  });
});
app.post("/deletekahootanswers", async (req, res) => {
  let kahootid = req.body["kahootid"];
  let email = req.body["email"];
  let post = {};
  let resBody = {};
  let sql = `DELETE FROM moodle.mdl_local_kahootanswers WHERE kahootid="${kahootid}" and email="${email}";`;
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    resBody["status"] = "true";
    res.send(resBody);
  });
});

app.post("/createquestions", async (req, res) => {
  let myArr = [];
  for (let i = 1; i < 11; i++) {
    myArr.push(`question${i}`);
  }
  let data = req.body["data"];
  let totalQuest = Object.keys(data).length;
  console.log(data);
  console.log(totalQuest);

  for (let i = 0; i < totalQuest; i++) {
    let index = myArr[i];
    var currentTimeInMilliseconds = Date.now();
    let post = {};
    post["id"] = currentTimeInMilliseconds + i + 1;
    post["kahootid"] = data[index]["kahootid"];
    post["question"] = data[index]["question"];
    post["questionid"] = i + 1;
    post["a"] = data[index]["a"];
    post["b"] = data[index]["b"];
    post["c"] = data[index]["c"];
    post["d"] = data[index]["d"];
    post["answer"] = data[index]["answer"];
    post["date"] = currentTimeInMilliseconds;

    let sql = "INSERT INTO mdl_local_kahootquestions SET ?";
    db.query(sql, post, (error, results, fields) => {
      if (error) throw error;
      console.log("The solution is: ", results);
    });
  }
  let resBody = { status: true };
  res.send(resBody);
});
//--

//-- Elif
app.post("/savetime", async (req, res) => {
  let totalMinute = req.body["totalMinute"];
  let email = req.body["email"];
  let post = {};
  var currentTimeInMilliseconds = Date.now();
  post["id"] = currentTimeInMilliseconds;
  post["email"] = email;
  post["totalminute"] = totalMinute;
  post["date"] = currentTimeInMilliseconds;
  let sql = "INSERT INTO mdl_local_focusmode SET ?";
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
  });
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.sendStatus(200);
});
app.post("/lasttime", async (req, res) => {
  let email = req.body["email"];
  let post = {};

  let sql = `SELECT * FROM moodle.mdl_local_focusmode ORDER BY date DESC LIMIT 1;`;
  let sql2 = `SELECT totalminute FROM moodle.mdl_local_focusmode WHERE email="${email}";`;
  db.query(sql2, post, (error, results, fields) => {
    if (error) throw error;
    let arr = [];
    let resBody = {};
    let sum = 0;
    for (
      let index = 0;
      index < JSON.parse(JSON.stringify(results)).length;
      index++
    ) {
      sum += parseFloat(
        JSON.parse(JSON.stringify(results))[index]["totalminute"]
      );
    }
    console.log(sum);
    arr.push(sum);
    db.query(sql, post, (error, results, fields) => {
      if (error) throw error;
      console.log("k");
      console.log(JSON.parse(JSON.stringify(results)));
      if (JSON.parse(JSON.stringify(results)).length != 0)
        arr.push(JSON.parse(JSON.stringify(results))[0]["totalminute"]);
      else arr.push(0);
      console.log(arr);
      resBody["data"] = arr;
      res.setHeader("Access-Control-Allow-Origin", "*");
      res.send(resBody);
    });
  });
});
//
app.post("/obsrecords", async (req, res) => {
  let examid = req.body["examid"];
  let post = {};
  let resBody = {};
  let sql = `SELECT * FROM mdl_block_examsobs WHERE examid="${examid}"`;
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    resBody["arr"] = JSON.parse(JSON.stringify(results));
    res.send(resBody);
  });
});

app.listen(process.env.PORT || "3000", () => {
  console.log("server is running..");
});

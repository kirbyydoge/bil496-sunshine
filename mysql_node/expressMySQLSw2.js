const express = require("express");
const mysql = require("mysql");
const app = express();

const db = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "moodle",
});

app.use(express.json());

app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "GET,POST,PUT,PATCH,DELETE");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type, Authorization");
  next();
});

///--
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

////----
app.listen(process.env.PORT || "3000", () => {
  console.log("server is running..");
});

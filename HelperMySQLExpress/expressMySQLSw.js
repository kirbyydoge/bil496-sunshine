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

app.listen(process.env.PORT || "3000", () => {
  console.log("server is running..");
});

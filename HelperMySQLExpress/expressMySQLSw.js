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
app.post("/kahootquestions", (req, res) => {
  var currentTimeInMilliseconds = Date.now();
  let post = {};
  post["id"] = currentTimeInMilliseconds;
  post["kahootid"] = req.body["kahootid"];
  post["question"] = req.body["question"];
  post["questionid"] = req.body["questionid"];
  post["date"] = currentTimeInMilliseconds.toString();
  post["answer"] = req.body["answer"];
  let sql = "INSERT INTO mdl_local_kahootquestions SET ?";
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    console.log("The solution is: ", results);
  });
  res.sendStatus(200);
});
app.post("/kahootanswer", (req, res) => {
  var currentTimeInMilliseconds = Date.now();
  let post = {};
  post["id"] = currentTimeInMilliseconds;
  post["kahootid"] = req.body["kahootid"];
  post["question"] = req.body["question"];
  post["date"] = currentTimeInMilliseconds.toString();
  post["answer"] = req.body["answer"];
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
  let sql = `SELECT COUNT(A.email) FROM mdl_local_kahootanswers as A INNER JOIN mdl_local_kahootquestions as B ON  B.kahootid=A.kahootid and A.questionid=B.questionid and A.answer=B.answer WHERE A.kahootid="${kahootid}" and A.email="${email}";`;
  db.query(sql, post, (error, results, fields) => {
    if (error) throw error;
    resBody["count"] = JSON.parse(JSON.stringify(results))[0]["COUNT(A.email)"];
    res.send(resBody);
  });
});

app.listen(process.env.PORT || "3000", () => {
  console.log("server is running..");
});

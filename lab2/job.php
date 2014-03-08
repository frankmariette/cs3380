<!DOCTYPE html>
<html>
<head>
  <title>getElementById example</title>
  <script>
    var Person = {
     name: "Oberta",
     age: 0,
      
      updateAge: function() {
          var newAge = document.getElementById("ageInput");
          if (newAge !== this.age) {
              this.age = newAge; //set to new age
          }
      }
  }
  
  console.log(Person);
  window.onload = function(){(document.getElementById("submitAge").addEventListener("click", Person.updateAge(), true));};
  console.log(Person);
  </script>
</head>
<body>
<input type="text" id="ageInput" />
<input type="submit" id="submitAge" value="submit">
</body>
</html>
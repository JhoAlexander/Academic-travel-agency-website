 function obtenerSaludo() {                   // Define una función llamada obtenerSaludo
      const ahora = new Date();               // Crea un objeto Date con la fecha y hora actual
      const hora = ahora.getHours();          // obtiene la hora actual (0-23)

      let saludo;

      if (hora >= 6 && hora < 12) {                      
        saludo = "¡Buenos días, futuro viajero! 🌞";       // Declara una variable para almacenar el saludo
      } else if (hora >= 12 && hora < 18) {
        saludo = "¡Buenas tardes, aventurero! 🌴⛵";
      } else {
        saludo = "¡Buenas noches, soñador de Hawai! 🌙";
      }

      return saludo;  // Devuelve el saludo correspondiente
    }

    // Mostrar el saludo en la página
    document.getElementById("saludo").textContent = obtenerSaludo(); // Coloca el saludo dentro del elemento con id="saludo"

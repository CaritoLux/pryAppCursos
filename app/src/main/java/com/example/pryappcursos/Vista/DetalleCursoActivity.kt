package com.example.pryappcursos.Vista

import android.os.Bundle
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import com.bumptech.glide.Glide
import com.example.pryappcursos.Interfaceapi.RetrofitCliente
import com.example.pryappcursos.Modelo.dataDetalleCurso
import com.example.pryappcursos.Presentador.clsDetalleCursoPresentador
import com.example.pryappcursos.R
class DetalleCursoActivity : AppCompatActivity() {
    private lateinit var presenter: clsDetalleCursoPresentador
    private lateinit var imageView: ImageView
    private lateinit var txtNombre: TextView
    private lateinit var txtCodigo: TextView
    private lateinit var txtDescripcion: TextView
    private lateinit var txtEspacio: TextView
    private lateinit var txtHoras: TextView
    private lateinit var txtDias: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_detalle_curso)
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        imageView = findViewById(R.id.imageView)
        txtNombre = findViewById(R.id.txtNombre)
        txtCodigo = findViewById(R.id.txtCodigo)
        txtDescripcion = findViewById(R.id.txtDescripcion)
        txtEspacio = findViewById(R.id.txtEspacio)
        txtHoras = findViewById(R.id.txtHoras)
        txtDias = findViewById(R.id.txtDias)

        // obtiene el ID enviado desde el catálogo
        val idCurso = intent.getIntExtra("ID_CURSO", 0)
        presenter = clsDetalleCursoPresentador(this)

        if (idCurso != 0) {
            presenter.cargarDetalle(idCurso)
        } else {
            mostrarError("No llegó el ID del curso")
        }
    }

    fun mostrarDetalle(curso: dataDetalleCurso) {
        txtNombre.text = curso.nombre
        txtCodigo.text = "Código: " + curso.codigo
        txtDescripcion.text = curso.descripcion
        txtEspacio.text = curso.espacio
        txtHoras.text = curso.total_horas.toString() + " Horas"
        txtDias.text = curso.dia_semana + " (" + curso.hora_inicio + " - " + curso.hora_fin + " hrs)"

        // URL
        val baseUrl = RetrofitCliente.BASE_URL
        val urlCompleta = baseUrl + curso.imagen_url

        Glide.with(this)
            .load(urlCompleta)
            .placeholder(android.R.drawable.ic_menu_gallery)
            .error(android.R.drawable.stat_notify_error)
            .into(imageView)
    }

    fun mostrarError(mensaje: String) {
        Toast.makeText(this, mensaje, Toast.LENGTH_LONG).show()
    }
}
import '../css/front.scss'
import 'bootstrap/dist/js/bootstrap'

const scriptToLoad = document.body.dataset.scriptToLoad
if (scriptToLoad !== '') {
  const [filename, extension] = scriptToLoad.split('.')

  switch (extension) {
    case 'js':
      import(`./front/pages/${filename}.js`).then(null)
      break
    case 'jsx':
      import(`./front/pages/${filename}.jsx`).then(null)
      break
  }
}

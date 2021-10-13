const templates = [
  {
    input: 'index.html',
    output: '', // Replace 'views' to your remote project views path
    renameOptions: {
      basename: 'index',
      extname: '.html'
    }
  }
];

module.exports = function publish(mix) {
  if (mix.env.isProd) {
    mix.publish();
    // Publish templates
    //mix.publish(templates);
  }
};

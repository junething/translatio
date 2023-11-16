/* This section of the code registers a new block, sets an icon and a category, and indicates what type of fields it'll include. */
  
wp.blocks.registerBlockType('tmy/tmy-chooser-box', {
  title: 'TMY Language Switcher Block',
  icon: 'translation',
  category: 'common',
  attributes: {
    content: {type: 'string'},
    color: {type: 'string'}
  },
  
/* This configures how the content and color fields will work, and sets up the necessary elements */
  
  edit: function(props) {
    return React.createElement(
      "div",
      { style: { border: "2px solid #00a6d3" } },
      "TMY Language Switcher Block",
    );
  },
  save: function(props) {
    return wp.element.createElement(
      "div",
      { style: { border: "2px solid #00a6d3" } },
      "TMY Language Switcher Block"
    );
  }
})

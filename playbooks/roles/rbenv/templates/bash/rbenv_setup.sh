{% if system_wide %}
export RBENV_ROOT="{{ rbenv.install_dir }}"
export PATH="${RBENV_ROOT}bin:${PATH}"
eval "$(rbenv init -)"
{% else %}
if [ -d "$HOME/.rbenv/" ]; then
  export RBENV_ROOT="$HOME/.rbenv/"
  export PATH="${RBENV_ROOT}bin:${PATH}"
  eval "$(rbenv init -)"
fi
{% endif %}
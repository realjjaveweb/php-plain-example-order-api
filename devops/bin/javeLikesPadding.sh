#!/bin/sh

# DEFAULTS
TEXT_COLOR="37" # white
BG_COLOR="44"   # blue (light blue)
PADDING=$(printf "%*s" 10 "") # N spaces
BOLD="0"        # Default to no bold

# OPTIONS
while [ "$1" != "" ]; do
    case $1 in
        --color=*)
            COLOR_OPTION="${1#*=}"
            case $COLOR_OPTION in
                white) TEXT_COLOR="37" ;;
                black) TEXT_COLOR="30" ;;
                purple) TEXT_COLOR="35" ;;
                *) echo "Invalid --color option"; exit 1 ;;
            esac
            ;;
        --bg=*)
            BG_OPTION="${1#*=}"
            case $BG_OPTION in
                blue) BG_COLOR="44" ;;
                green) BG_COLOR="42" ;;
                red) BG_COLOR="41" ;;
                yellow) BG_COLOR="43" ;;
                white) BG_COLOR="47" ;;
                purple) BG_COLOR="45" ;;
                pink) BG_COLOR="105" ;;
                transparent) BG_COLOR="49" ;; # 49 = default bg; 0 would reset all
                *) echo "Invalid --bg option"; exit 1 ;;
            esac
            ;;
        --bold)
            BOLD="1"
            ;;
        *)
            TEXT="$1"
            ;;
    esac
    shift
done

# empty text? => display help
if [ -z "$TEXT" ]; then
    echo "Usage: $0 [--color=white|black] [--bg=blue|green|red|yellow|white|purple|pink] [--bold] \"Your text here\""
    exit 1
fi

# PADDING (padding & calculations)
PADDED_TEXT="${PADDING}${TEXT}${PADDING}"
LINE_LENGTH=${#PADDED_TEXT} # good for simple text, bad with emojis
DISPLAY_WIDTH=$(php -r "echo mb_strwidth('$PADDED_TEXT', 'UTF-8');")
# NUMBER_OF_EMOJIS=$(php -r "echo preg_match_all('/[\x{1F300}-\x{1F6FF}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '$PADDED_TEXT');")
# a naive correction... probably best way would be a better display width tool, since different emojis have different widths
# DISPLAY_WIDTH=$((DISPLAY_WIDTH - NUMBER_OF_EMOJIS))
# python version of similar (more precise, but needs python3-wcwidth to be installed)
# DISPLAY_WIDTH=$(python3 -c "import sys; from wcwidth import wcswidth; print(wcswidth(sys.argv[1]))" "${PADDED_TEXT}")
# debug only:
# echo "DISPLAY_WIDTH: $DISPLAY_WIDTH"
# echo "LINE_LENGTH: $LINE_LENGTH"
# echo "NUMBER_OF_EMOJIS: $NUMBER_OF_EMOJIS"
EMPTY_LINE=$(printf "%${DISPLAY_WIDTH}s" " ")

# 🦋 THE PRINT 🦋
echo "\033[${TEXT_COLOR};${BG_COLOR}m${EMPTY_LINE}\033[0m"  # No bold for empty lines
echo "\033[${BOLD};${TEXT_COLOR};${BG_COLOR}m${PADDED_TEXT}\033[0m"
echo "\033[${TEXT_COLOR};${BG_COLOR}m${EMPTY_LINE}\033[0m"  # No bold for empty lines

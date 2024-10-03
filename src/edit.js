/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';
import React from 'react';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, useEffect } from 'react';
import Select, { components as SelectComponents } from 'react-select';
import { SortableContainer, SortableElement, SortableHandle } from 'react-sortable-hoc';

const { InspectorControls } = wp.blockEditor;
const { PanelBody, RangeControl,SelectControl} = wp.components;
const { useSelect } = wp.data;
/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
const Edit = ({ attributes, setAttributes }) => {
	const SortableSelect = SortableContainer(Select);
	const [selected, setSelected] = useState([]);
	const { postCount,  selectedShow, selectedVideos } = attributes;
	const shows = useSelect(select => select('core').getEntityRecords('taxonomy', 'shows', { per_page: -1 }), []);
	const videos = useSelect(select => {
		return selectedShow ? select('core').getEntityRecords('postType', 'video', { per_page: -1, shows: selectedShow }) : [];
	}, [selectedShow]);
    const onChangeSelectedShow = newSelectedShow => {
        setSelected([]); // Reset selected options
        setAttributes({ selectedShow: newSelectedShow });
        setTimeout(() => {
            setAttributes({ selectedVideos: [] }); // Reset selectedVideos after selectedShow has been updated
        },500);
    };
	const onChangeSelectedVideos = selectedVideos => {
		setSelected(selectedVideos);
		setAttributes( { selectedVideos: selectedVideos.map(selectedVideos => selectedVideos.value)} );
	};
	const onChangePostCount = newPostCount => {
		setAttributes({ postCount: newPostCount });
	};
	function arrayMove(array, from, to) {
		const slicedArray = array.slice();
		slicedArray.splice(
			to < 0 ? array.length + to : to,
			0,
			slicedArray.splice(from, 1)[0]
		);
		return slicedArray;
	}
	const SortableMultiValue = SortableElement(
	  (props) => {
		const onMouseDown = (e) => {
		  e.preventDefault();
		  e.stopPropagation();
		};
		const innerProps = { ...props.innerProps, onMouseDown };
		return <SelectComponents.MultiValue {...props} innerProps={innerProps} />;
	  }
	);
	const SortableMultiValueLabel = SortableHandle(
	  (props) => <SelectComponents.MultiValueLabel {...props} />
	);
	const onSortEnd = ({ oldIndex, newIndex }) => {
		const newValue = arrayMove(selected, oldIndex, newIndex);
		setSelected(newValue);
		const sortedValues = newValue.map((item) => item.value);
		setAttributes( { selectedVideos: sortedValues} );

	};
	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<SelectControl
						label={ __( 'Select Video Show' ) }
						value={selectedShow}
						options={shows && [{ label: 'Select any Show', value: '' }, ...shows.map(show => ({ label: show.name, value: show.id }))]}
						onChange={onChangeSelectedShow}
					/>
					{ selectedShow &&
					<SortableSelect
						// label={ __( 'Select Video' ) }
						useDragHandle
						axis="xy"
						onSortEnd={onSortEnd}
						distance={4}
						getHelperDimensions={({ node }) => node.getBoundingClientRect()}
						isMulti
						options={videos && [{ label: 'Select any Video', value: '' }, ...videos.map(video => ({ label: video.title.rendered, value: video.id }))]}
						value={selected}
						onChange={onChangeSelectedVideos}
						components={{
							MultiValue: SortableMultiValue,
							MultiValueLabel: SortableMultiValueLabel,
						}}
						closeMenuOnSelect={false}
					/>
					}
					<RangeControl
						label={ __( 'Number of Videos to Display' ) }
						value={postCount}
						onChange={onChangePostCount}
						min={1}
						max={10}
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="create-block/video-shows"
				attributes={{ postCount, selectedShow,selectedVideos }}
			/>
		</>
	);
};
export default Edit;